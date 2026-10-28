<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\HelpdeskService;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Config;

class FetchInboundEmails extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'helpdesk:fetch-emails {--mock : Run in simulated demo mode with high-fidelity mock email data}';

    /**
     * The console command description.
     */
    protected $signature_description = 'Fetch inbound support emails from IMAP or process mock data, classify categories, and create tickets.';

    protected $helpdeskService;

    public function __construct(HelpdeskService $helpdeskService)
    {
        parent::__construct();
        $this->helpdeskService = $helpdeskService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("=== Inbound Email Ingestor ===");

        $isMockMode = $this->option('mock');
        
        // Check if real config exists in env, otherwise fallback to mock automatically with a notice
        $host = env('IMAP_HOST');
        $username = env('IMAP_USERNAME');
        $password = env('IMAP_PASSWORD');

        if (!$isMockMode && ($host && $username && $password)) {
            $this->info("Attempting connection to live IMAP server: {$host}");
            $this->processLiveEmails($host, $username, $password);
        } else {
            if (!$isMockMode) {
                $this->warn("IMAP credentials not found in .env. Falling back to Mock Demo mode.");
            } else {
                $this->info("Running in Mock Demo Mode...");
            }
            $this->processMockEmails();
        }

        $this->info("Ingestion completed successfully!");
        return Command::SUCCESS;
    }

    /**
     * Process mock emails to demonstrate parsing and classification.
     */
    protected function processMockEmails(): void
    {
        $mockEmails = [
            [
                'from_email' => 'sarah.connor@cyberdyne.com',
                'from_name' => 'Sarah Connor',
                'customer_tier' => 'Gold',
                'customer_company' => 'Cyberdyne Systems',
                'subject' => 'URGENT: double charged on invoice INV-9902',
                'body' => "Hi support team, I noticed I was charged twice on my credit card for invoice INV-9902. Please refund the duplicate amount as soon as possible. Thank you, Sarah.",
                'priority' => 'High',
            ],
            [
                'from_email' => 'neo@matrix.io',
                'from_name' => 'Thomas Anderson',
                'customer_tier' => 'Platinum',
                'customer_company' => 'Matrix Operations',
                'subject' => 'CRITICAL: Database server connection failure',
                'body' => "Our production database is throwing 500 connection refused errors when attempting to authenticate. We cannot process checkouts. Urgent assistance needed.",
                'priority' => 'Urgent',
            ],
            [
                'from_email' => 'alice@wunderland.net',
                'from_name' => 'Alice Liddell',
                'customer_tier' => 'Standard',
                'customer_company' => 'Wonderland Ltd',
                'subject' => 'Question about export analytics features',
                'body' => "Hello, I am wondering if there is a way to export goal tracking reports as CSV format. Let me know where I can find the download link. Thanks!",
                'priority' => 'Low',
            ]
        ];

        $headers = ['From', 'Subject', 'Detected Category', 'Routed Agent', 'Ticket Key'];
        $rows = [];

        foreach ($mockEmails as $mail) {
            // 1. Detect Category
            $category = $this->helpdeskService->classifyEmail($mail['subject'], $mail['body']);

            // 2. Log and create ticket
            $ticket = $this->helpdeskService->createTicket([
                'customer_name' => $mail['from_name'],
                'customer_email' => $mail['from_email'],
                'customer_phone' => null,
                'customer_company' => $mail['customer_company'],
                'customer_tier' => $mail['customer_tier'],
                'subject' => $mail['subject'],
                'description' => $mail['body'],
                'category' => $category,
                'priority' => $mail['priority'],
                'channel' => 'Email',
            ]);

            $routedAgent = $ticket->assignedTo?->name ?? 'Unassigned';

            $rows[] = [
                "{$mail['from_name']} <{$mail['from_email']}>",
                $mail['subject'],
                $category,
                $routedAgent,
                $ticket->ticket_key
            ];
        }

        $this->table($headers, $rows);
    }

    /**
     * Connect to live IMAP server and ingest real emails.
     */
    protected function processLiveEmails(string $host, string $username, string $password): void
    {
        if (!function_exists('imap_open')) {
            $this->error("PHP IMAP extension is not enabled. Cannot run live fetch. Please enable php_imap in php.ini");
            return;
        }

        $port = env('IMAP_PORT', 993);
        $encryption = env('IMAP_ENCRYPTION', 'ssl'); // ssl, tls, or none
        
        $sslFlag = $encryption === 'ssl' ? '/ssl' : ($encryption === 'tls' ? '/tls' : '/novalidate-cert');
        $mailboxPath = "{" . $host . ":" . $port . "/imap" . $sslFlag . "}INBOX";

        $mbox = @imap_open($mailboxPath, $username, $password);

        if (!$mbox) {
            $this->error("IMAP Connection failed: " . imap_last_error());
            return;
        }

        $emails = imap_search($mbox, 'UNSEEN');

        if (!$emails) {
            $this->info("No new unread support emails found.");
            imap_close($mbox);
            return;
        }

        $this->info("Found " . count($emails) . " unread email(s). Processing...");

        foreach ($emails as $emailNumber) {
            $overview = imap_fetch_overview($mbox, $emailNumber, 0);
            $structure = imap_fetchstructure($mbox, $emailNumber);
            
            $subject = isset($overview[0]->subject) ? imap_utf8($overview[0]->subject) : 'No Subject';
            $from = isset($overview[0]->from) ? imap_utf8($overview[0]->from) : '';
            
            // Extract email and name from "From" header
            $senderEmail = 'unknown@unknown.com';
            $senderName = 'Unknown Sender';
            if (preg_match('/(.*?)<(.*?)>/', $from, $matches)) {
                $senderName = trim($matches[1]);
                $senderEmail = trim($matches[2]);
            } else {
                $senderEmail = trim($from);
                $senderName = trim($from);
            }

            // Fetch Email Body
            $body = $this->getEmailBody($mbox, $emailNumber, $structure);

            // 1. Detect Category
            $category = $this->helpdeskService->classifyEmail($subject, $body);

            // 2. Fetch existing customer tier if any
            $customer = Customer::where('email', $senderEmail)->first();
            $tier = $customer ? $customer->tier : 'Standard';
            $company = $customer ? $customer->company : null;

            // 3. Create ticket
            $ticket = $this->helpdeskService->createTicket([
                'customer_name' => $senderName,
                'customer_email' => $senderEmail,
                'customer_phone' => null,
                'customer_company' => $company,
                'customer_tier' => $tier,
                'subject' => $subject,
                'description' => $body ?: 'Empty email body.',
                'category' => $category,
                'priority' => 'Medium', // Default initial priority
                'channel' => 'Email',
            ]);

            // Mark as read (SEEN)
            imap_setflag_full($mbox, $emailNumber, "\\Seen");

            $routedAgent = $ticket->assignedTo?->name ?? 'Unassigned';
            $this->line("Processed Email: [{$ticket->ticket_key}] '{$subject}' | Routed: {$routedAgent} (Category: {$category})");
        }

        imap_close($mbox);
    }

    /**
     * Helper to read email body content recursively.
     */
    protected function getEmailBody($mbox, $emailNumber, $structure, $partNumber = ""): string
    {
        if ($structure->type == 0) { // Text
            return imap_fetchbody($mbox, $emailNumber, $partNumber ?: "1");
        }

        if ($structure->type == 1) { // Multipart
            foreach ($structure->parts as $index => $part) {
                $subPartNumber = $partNumber ? $partNumber . "." . ($index + 1) : ($index + 1);
                $body = $this->getEmailBody($mbox, $emailNumber, $part, $subPartNumber);
                if ($body) return $body;
            }
        }

        return '';
    }
}
