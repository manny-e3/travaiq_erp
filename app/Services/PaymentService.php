<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Project;
use App\Models\User;
use App\Mail\AccountCreatedMail;
use App\Events\PaymentSuccessful;
use App\Events\ProjectStageUpdated;

class PaymentService
{
    protected $contractService;

    public function __construct(ContractService $contractService)
    {
        $this->contractService = $contractService;
    }

    /**
     * Initialize payment and get redirect URL
     */
    public function initializePayment(Invoice $invoice, string $gatewaySlug, $customAmount = null)
    {
        $gateway = PaymentGateway::where('slug', $gatewaySlug)->first();
        
        if (!$gateway || !$gateway->is_active) {
            throw new \Exception("Payment gateway not available.");
        }

        $amount = $customAmount ?? $invoice->amount;

        switch ($gatewaySlug) {
            case 'paystack':
                return $this->initializePaystack($invoice, $gateway->config, $amount);
            case 'flutterwave':
                return $this->initializeFlutterwave($invoice, $gateway->config, $amount);
            case 'stripe':
                return $this->initializeStripe($invoice, $gateway->config, $amount);
            case 'paypal':
                return $this->initializePaypal($invoice, $gateway->config, $amount);
            default:
                throw new \Exception("Gateway integration pending.");
        }
    }

    private function initializePaystack(Invoice $invoice, array $config, $amount)
    {
        $url = "https://api.paystack.co/transaction/initialize";

        if (empty($config['secret_key'])) {
            throw new \Exception("Paystack Secret Key is missing in Payment Settings.");
        }

        $reference = $invoice->invoice_number . '_' . time();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $config['secret_key'],
            'Content-Type'  => 'application/json',
            'User-Agent'    => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Expect'        => '',
        ])
        ->withOptions([
            'verify' => false,
            'curl' => [
                CURLOPT_SSLVERSION      => CURL_SSLVERSION_TLSv1_2,
                CURLOPT_IPRESOLVE       => CURL_IPRESOLVE_V4,
                CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            ]
        ])
        ->post($url, [
            'email'        => $invoice->prospect->email,
            'amount'       => $amount * 100,
            'reference'    => $reference,
            'callback_url' => route('payments.callback', ['gateway' => 'paystack']),
            'metadata'     => [
                'invoice_id'  => $invoice->id,
                'prospect_id' => $invoice->prospect_id,
            ],
        ]);

        if ($response->successful()) {
            $data = $response->json()['data'];

            Transaction::create([
                'invoice_id'            => $invoice->id,
                'gateway_slug'          => 'paystack',
                'transaction_reference' => $reference,
                'amount'                => $amount,
                'status'                => 'pending',
            ]);

            return $data['authorization_url'];
        }

        Log::error('Paystack Init Failed', [
            'status'  => $response->status(),
            'body'    => $response->body(),
            'invoice' => $invoice->id,
        ]);

        throw new \Exception("Could not initialize Paystack payment.");
    }

    private function initializeFlutterwave(Invoice $invoice, array $config, $amount)
    {
        $url = "https://api.flutterwave.com/v3/payments";
        
        if (empty($config['secret_key'])) {
            throw new \Exception("Flutterwave Secret Key is missing in Payment Settings.");
        }
        
        $txRef = $invoice->invoice_number . '_' . time();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $config['secret_key'],
            'Content-Type'  => 'application/json',
            'User-Agent'    => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Expect'        => '',
        ])
        ->withOptions([
            'verify' => false,
            'curl' => [
                CURLOPT_SSLVERSION   => CURL_SSLVERSION_TLSv1_2,
                CURLOPT_IPRESOLVE    => CURL_IPRESOLVE_V4,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            ]
        ])
        ->post($url, [
            'tx_ref' => $txRef,
            'amount' => $amount,
            'currency' => "NGN",
            'redirect_url' => route('payments.callback', ['gateway' => 'flutterwave']),
            'customer' => [
                'email' => $invoice->prospect->email,
                'phonenumber' => $invoice->prospect->phone_number,
                'name' => $invoice->prospect->name,
            ],
            'meta' => [
                'invoice_id' => $invoice->id,
            ],
            'customizations' => [
                'title' => "The Curated Archive",
                'description' => "Payment for Invoice " . $invoice->invoice_number,
            ]
        ]);

        if ($response->successful()) {
            $data = $response->json()['data'];

            Transaction::create([
                'invoice_id' => $invoice->id,
                'gateway_slug' => 'flutterwave',
                'transaction_reference' => $txRef,
                'amount' => $amount,
                'status' => 'pending'
            ]);

            return $data['link'];
        }

        Log::error('Flutterwave Init Failed', [
            'status'  => $response->status(),
            'body'    => $response->body(),
            'invoice' => $invoice->id,
        ]);
        throw new \Exception("Could not initialize Flutterwave payment.");
    }

    private function initializeStripe(Invoice $invoice, array $config, $amount)
    {
        $url = "https://api.stripe.com/v1/checkout/sessions";

        if (empty($config['secret_key'])) {
            throw new \Exception("Stripe Secret Key is missing in Payment Settings.");
        }

        $reference = $invoice->invoice_number . '_' . time();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $config['secret_key'],
            'Content-Type'  => 'application/x-www-form-urlencoded',
        ])
        ->asForm()
        ->post($url, [
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Invoice ' . $invoice->invoice_number,
                        ],
                        'unit_amount' => (int)($amount * 100),
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'success_url' => route('payments.callback', ['gateway' => 'stripe']) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payments.checkout', ['invoice' => $invoice->id]),
            'metadata' => [
                'invoice_id' => $invoice->id,
            ],
        ]);

        if ($response->successful()) {
            $data = $response->json();

            Transaction::create([
                'invoice_id'            => $invoice->id,
                'gateway_slug'          => 'stripe',
                'transaction_reference' => $data['id'],
                'amount'                => $amount,
                'status'                => 'pending',
            ]);

            return $data['url'];
        }

        Log::error('Stripe Init Failed', [
            'status'  => $response->status(),
            'body'    => $response->body(),
            'invoice' => $invoice->id,
        ]);

        throw new \Exception("Could not initialize Stripe payment.");
    }

    private function initializePaypal(Invoice $invoice, array $config, $amount)
    {
        if (empty($config['public_key']) || empty($config['secret_key'])) {
            throw new \Exception("PayPal Client ID or Secret Key is missing in Payment Settings.");
        }

        // 1. Get Access Token
        $tokenUrl = "https://api-m.sandbox.paypal.com/v1/oauth2/token";
        
        $tokenResponse = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($config['public_key'] . ':' . $config['secret_key']),
            'Content-Type'  => 'application/x-www-form-urlencoded',
        ])
        ->asForm()
        ->post($tokenUrl, [
            'grant_type' => 'client_credentials'
        ]);

        if (!$tokenResponse->successful()) {
            Log::error('PayPal Token Retrieval Failed', [
                'status' => $tokenResponse->status(),
                'body'   => $tokenResponse->body(),
            ]);
            throw new \Exception("Could not authenticate with PayPal.");
        }

        $accessToken = $tokenResponse->json()['access_token'];

        // 2. Create Order
        $orderUrl = "https://api-m.sandbox.paypal.com/v2/checkout/orders";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type'  => 'application/json',
        ])
        ->post($orderUrl, [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => number_format($amount, 2, '.', ''),
                    ],
                    'custom_id' => (string)$invoice->id,
                    'description' => 'Payment for Invoice ' . $invoice->invoice_number,
                ]
            ],
            'application_context' => [
                'return_url' => route('payments.callback', ['gateway' => 'paypal']),
                'cancel_url' => route('payments.checkout', ['invoice' => $invoice->id]),
            ]
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $orderId = $data['id'];

            // Find approval link
            $approvalUrl = null;
            foreach ($data['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    $approvalUrl = $link['href'];
                    break;
                }
            }

            if ($approvalUrl) {
                Transaction::create([
                    'invoice_id'            => $invoice->id,
                    'gateway_slug'          => 'paypal',
                    'transaction_reference' => $orderId,
                    'amount'                => $amount,
                    'status'                => 'pending',
                ]);

                return $approvalUrl;
            }
        }

        Log::error('PayPal Order Init Failed', [
            'status'  => $response->status(),
            'body'    => $response->body(),
            'invoice' => $invoice->id,
        ]);

        throw new \Exception("Could not initialize PayPal payment.");
    }

    /**
     * Handle Webhook Verification
     */
    public function verifyWebhook(string $gatewaySlug, $request)
    {
        $gateway = PaymentGateway::where('slug', $gatewaySlug)->first();
        if (!$gateway) return false;

        switch ($gatewaySlug) {
            case 'paystack':
                return $this->verifyPaystackWebhook($request, $gateway->config['secret_key'] ?? '');
            case 'flutterwave':
                return $this->verifyFlutterwaveWebhook($request, $gateway->config['secret_key'] ?? '');
            case 'stripe':
                return $this->verifyStripeWebhook($request, $gateway->config['secret_key'] ?? '');
            case 'paypal':
                return $this->verifyPaypalWebhook($request, $gateway->config['secret_key'] ?? '');
            default:
                return false;
        }
    }

    private function verifyPaystackWebhook($request, $secretKey)
    {
        $signature = $request->header('x-paystack-signature');
        if (!$signature) return false;

        $computedSignature = hash_hmac('sha512', $request->getContent(), $secretKey);
        return hash_equals($signature, $computedSignature);
    }

    private function verifyFlutterwaveWebhook($request, $secretKey)
    {
        // Flutterwave uses a secret hash set in the dashboard
        $signature = $request->header('verif-hash');
        // For simplicity, we compare with secret_key or a dedicated webhook_hash
        return $signature === $secretKey; 
    }

    private function verifyStripeWebhook($request, $secretKey)
    {
        $signature = $request->header('stripe-signature');
        return !empty($signature);
    }

    private function verifyPaypalWebhook($request, $secretKey)
    {
        return true;
    }

    /**
     * Complete the payment process: update invoice, activate project, create user.
     */
    public function finalizePayment(Invoice $invoice, string $reference)
    {
        // 1. Get transaction to identify amount paid
        $transaction = Transaction::where('transaction_reference', $reference)
            ->orWhere('external_reference', $reference)
            ->first();

        $amountPaid = $transaction ? $transaction->amount : 0;
        $newTotalPaid = $invoice->total_paid + $amountPaid;
        
        // 2. Update Invoice status
        $invoice->update([
            'status' => 'paid',
            'total_paid' => $newTotalPaid,
            'is_installment' => $newTotalPaid < $invoice->amount,
            'paid_at' => now(),
            'payment_reference' => $reference,
        ]);

        $paymentType = 'initial';

        // 3. Activate Project if needed
        if ($invoice->prospect->status !== 'project_active') {
            $project = Project::create([
                'prospect_id' => $invoice->prospect_id,
                'payment_reference' => $invoice->payment_reference,
                'status' => 'editing'
            ]);

            // Auto-generate publishing agreement
            try {
                $this->contractService->generateContract($project);
            } catch (\Exception $e) {
                Log::error('Contract Auto-Generation Failed: ' . $e->getMessage());
            }

            // Notify via event
            event(new ProjectStageUpdated($project, 'editing'));

            // Update Prospect status
            $invoice->prospect->update(['status' => 'project_active']);
            
            // 4. Create User Account if not exists
            $this->ensureUserAccountCreated($invoice->prospect);
        } else {
            $paymentType = 'balance';
        }

        // 5. Notify success
        event(new PaymentSuccessful($invoice, $paymentType));

        return $paymentType;
    }

    /**
     * Ensure the author has an account.
     */
    private function ensureUserAccountCreated($prospect)
    {
        $user = User::where('email', $prospect->email)->first();
        if (!$user) {
            $generatedPassword = Str::random(10);
            $user = User::create([
                'name' => $prospect->name,
                'email' => $prospect->email,
                'password' => Hash::make($generatedPassword),
            ]);
            
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('prospect');
            }

            Mail::to($user->email)->send(new AccountCreatedMail($user, $generatedPassword));
        }
        return $user;
    }
}
