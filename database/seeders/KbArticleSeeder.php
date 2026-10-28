<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KbArticle;

class KbArticleSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            [
                'title' => 'How to Reset Your Account Password',
                'content' => "To reset your account password, follow these step-by-step instructions:
1. Navigate to the Login screen.
2. Click on the 'Forgot Password' link at the bottom of the login card.
3. Enter your registered email address and submit the request.
4. Check your inbox for a reset password link.
5. Click the link, set a new password containing at least 8 characters, a number, and a special symbol.
6. Return to login page and login with your new credentials.",
                'category' => 'Account',
                'tags' => ['password', 'profile', 'login', 'reset'],
                'is_published' => true,
                'views' => 120,
            ],
            [
                'title' => 'Resolving Database Connection Refused (500 Error)',
                'content' => "If your system is displaying '500 connection refused' or postgres database errors, troubleshoot with these steps:
1. Verify database server status: Ensure the PostgreSQL service is active and running on port 5432.
2. Check .env configuration: Verify DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, and DB_PASSWORD are correct.
3. Network Access: Ensure the local or database hosting firewall allows port 5432 ingress/egress.
4. Connection Pool: In high traffic, the pool might be full. Optimize DB_MAX_CONNECTIONS or check active connections using 'select count(*) from pg_stat_activity'.",
                'category' => 'Technical',
                'tags' => ['database', 'sql', 'postgres', 'connection', 'crashed'],
                'is_published' => true,
                'views' => 240,
            ],
            [
                'title' => 'Understanding Billing Cycles and Invoicing',
                'content' => "We charge on a monthly recurring basis:
1. Invoices are generated automatically on the 1st of every calendar month.
2. Direct debit or credit cards are charged on the invoice date.
3. Refund Policy: If you have been double charged or need to dispute an invoice charge, submit a billing request under 'Billing' category. Duplicate charges are refunded automatically within 3 to 5 business days.",
                'category' => 'Billing',
                'tags' => ['billing', 'invoice', 'charge', 'pricing', 'refund'],
                'is_published' => true,
                'views' => 85,
            ],
            [
                'title' => 'Setting Up VPN Gateway Access',
                'content' => "Follow these guidelines to configure VPN access for your local workstation:
1. Download our official VPN connection profile client.
2. Import the OpenVPN configuration settings.
3. Input your corporate credentials.
4. Troubleshooting DNS: If you cannot connect, check if your local router blocks UDP port 1194 or if the network adapter requires custom DNS server configurations (e.g. 8.8.8.8).",
                'category' => 'Technical',
                'tags' => ['vpn', 'network', 'connection', 'dns'],
                'is_published' => true,
                'views' => 150,
            ],
            [
                'title' => 'Integrating Webhooks and API Integrations',
                'content' => "Our developer API supports inbound integrations:
1. Authentication: Pass your secure Bearer API Token in the Authorization header of all API calls.
2. Webhooks: You can subscribe to ticket logs and appraisal updates via the integrations tab.
3. Retries: If your endpoint is down, our system will retry sending webhook payloads up to 5 times with exponential backoff.",
                'category' => 'General',
                'tags' => ['api', 'webhook', 'integration'],
                'is_published' => true,
                'views' => 95,
            ],
            [
                'title' => 'Submitting General Feedback and Product Ideas',
                'content' => "We value your feature recommendations:
1. Log into your dashboard.
2. Create a new support ticket and select the 'Feedback' category.
3. Provide details on your suggestion, current limitations, and how this idea would improve your workflow.
4. Product managers review recommendations monthly.",
                'category' => 'Feedback',
                'tags' => ['feedback', 'suggestion', 'idea', 'improvement'],
                'is_published' => true,
                'views' => 45,
            ]
        ];

        foreach ($articles as $article) {
            KbArticle::updateOrCreate(['title' => $article['title']], $article);
        }
    }
}
