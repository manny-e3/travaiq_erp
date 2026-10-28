<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Invoice;
use App\Models\Prospect;
use App\Models\PaymentGateway;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;

class PaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_stripe_payment_initialization_successful()
    {
        Http::fake([
            'https://api.stripe.com/v1/checkout/sessions' => Http::response([
                'id' => 'cs_test_session_id',
                'url' => 'https://checkout.stripe.com/pay/cs_test_session_id',
            ], 200)
        ]);

        $prospect = Prospect::create([
            'name' => 'Alexander Hamilton',
            'email' => 'alexander@hamilton.com',
            'phone_number' => '+13035550100',
            'book_title' => 'My Life and Times',
            'genre' => 'Biography',
            'stage_of_manuscript' => 'Draft',
            'number_of_words' => 50000,
            'agreement_name' => 'Alexander Hamilton',
            'agreement_terms' => true,
        ]);

        $invoice = Invoice::create([
            'prospect_id' => $prospect->id,
            'invoice_number' => 'INV-TEST1234',
            'amount' => 150.00,
            'status' => 'unpaid',
            'allowed_gateways' => ['stripe'],
            'min_deposit_percentage' => 100,
        ]);

        $gateway = PaymentGateway::create([
            'name' => 'Stripe',
            'slug' => 'stripe',
            'config' => [
                'public_key' => 'pk_test_stripe_key',
                'secret_key' => 'sk_test_stripe_key',
            ],
            'is_active' => true,
        ]);

        $response = $this->get(route('payments.initiate', [
            'invoice' => $invoice->id,
            'gateway' => 'stripe',
            'amount' => 150.00,
        ]));

        $response->assertRedirect('https://checkout.stripe.com/pay/cs_test_session_id');

        $this->assertDatabaseHas('transactions', [
            'invoice_id' => $invoice->id,
            'gateway_slug' => 'stripe',
            'transaction_reference' => 'cs_test_session_id',
            'amount' => 150.00,
            'status' => 'pending',
        ]);
    }

    public function test_paypal_payment_initialization_successful()
    {
        Http::fake([
            'https://api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response([
                'access_token' => 'mock_access_token_123',
            ], 200),
            'https://api-m.sandbox.paypal.com/v2/checkout/orders' => Http::response([
                'id' => 'mock_paypal_order_id',
                'links' => [
                    [
                        'href' => 'https://www.sandbox.paypal.com/checkoutnow?token=mock_paypal_order_id',
                        'rel' => 'approve',
                    ]
                ]
            ], 200)
        ]);

        $prospect = Prospect::create([
            'name' => 'Alexander Hamilton',
            'email' => 'alexander@hamilton.com',
            'phone_number' => '+13035550100',
            'book_title' => 'My Life and Times',
            'genre' => 'Biography',
            'stage_of_manuscript' => 'Draft',
            'number_of_words' => 50000,
            'agreement_name' => 'Alexander Hamilton',
            'agreement_terms' => true,
        ]);

        $invoice = Invoice::create([
            'prospect_id' => $prospect->id,
            'invoice_number' => 'INV-TEST1235',
            'amount' => 150.00,
            'status' => 'unpaid',
            'allowed_gateways' => ['paypal'],
            'min_deposit_percentage' => 100,
        ]);

        $gateway = PaymentGateway::create([
            'name' => 'PayPal',
            'slug' => 'paypal',
            'config' => [
                'public_key' => 'mock_client_id',
                'secret_key' => 'mock_secret_key',
            ],
            'is_active' => true,
        ]);

        $response = $this->get(route('payments.initiate', [
            'invoice' => $invoice->id,
            'gateway' => 'paypal',
            'amount' => 150.00,
        ]));

        $response->assertRedirect('https://www.sandbox.paypal.com/checkoutnow?token=mock_paypal_order_id');

        $this->assertDatabaseHas('transactions', [
            'invoice_id' => $invoice->id,
            'gateway_slug' => 'paypal',
            'transaction_reference' => 'mock_paypal_order_id',
            'amount' => 150.00,
            'status' => 'pending',
        ]);
    }

    public function test_stripe_webhook_processes_success()
    {
        $prospect = Prospect::create([
            'name' => 'Alexander Hamilton',
            'email' => 'alexander@hamilton.com',
            'phone_number' => '+13035550100',
            'book_title' => 'My Life and Times',
            'genre' => 'Biography',
            'stage_of_manuscript' => 'Draft',
            'number_of_words' => 50000,
            'agreement_name' => 'Alexander Hamilton',
            'agreement_terms' => true,
        ]);

        $invoice = Invoice::create([
            'prospect_id' => $prospect->id,
            'invoice_number' => 'INV-TEST1234',
            'amount' => 150.00,
            'status' => 'unpaid',
            'allowed_gateways' => ['stripe'],
            'min_deposit_percentage' => 100,
        ]);

        $gateway = PaymentGateway::create([
            'name' => 'Stripe',
            'slug' => 'stripe',
            'config' => [
                'public_key' => 'pk_test_stripe_key',
                'secret_key' => 'sk_test_stripe_key',
            ],
            'is_active' => true,
        ]);

        $transaction = Transaction::create([
            'invoice_id' => $invoice->id,
            'gateway_slug' => 'stripe',
            'transaction_reference' => 'cs_test_session_id',
            'amount' => 150.00,
            'status' => 'pending',
        ]);

        $payload = [
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_session_id',
                    'metadata' => [
                        'invoice_id' => $invoice->id,
                    ]
                ]
            ]
        ];

        $response = $this->withHeaders([
            'stripe-signature' => 'mock_signature',
        ])->postJson(route('payments.webhook', ['gateway' => 'stripe']), $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'successful',
            'external_reference' => 'cs_test_session_id',
        ]);

        $this->assertEquals('paid', $invoice->fresh()->status);
    }

    public function test_paypal_webhook_processes_success()
    {
        $prospect = Prospect::create([
            'name' => 'Alexander Hamilton',
            'email' => 'alexander@hamilton.com',
            'phone_number' => '+13035550100',
            'book_title' => 'My Life and Times',
            'genre' => 'Biography',
            'stage_of_manuscript' => 'Draft',
            'number_of_words' => 50000,
            'agreement_name' => 'Alexander Hamilton',
            'agreement_terms' => true,
        ]);

        $invoice = Invoice::create([
            'prospect_id' => $prospect->id,
            'invoice_number' => 'INV-TEST1235',
            'amount' => 150.00,
            'status' => 'unpaid',
            'allowed_gateways' => ['paypal'],
            'min_deposit_percentage' => 100,
        ]);

        $gateway = PaymentGateway::create([
            'name' => 'PayPal',
            'slug' => 'paypal',
            'config' => [
                'public_key' => 'mock_client_id',
                'secret_key' => 'mock_secret_key',
            ],
            'is_active' => true,
        ]);

        $transaction = Transaction::create([
            'invoice_id' => $invoice->id,
            'gateway_slug' => 'paypal',
            'transaction_reference' => 'mock_paypal_order_id',
            'amount' => 150.00,
            'status' => 'pending',
        ]);

        $payload = [
            'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
            'resource' => [
                'id' => 'mock_capture_id',
                'custom_id' => (string)$invoice->id,
            ]
        ];

        $response = $this->postJson(route('payments.webhook', ['gateway' => 'paypal']), $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'successful',
            'external_reference' => 'mock_capture_id',
        ]);

        $this->assertEquals('paid', $invoice->fresh()->status);
    }
}
