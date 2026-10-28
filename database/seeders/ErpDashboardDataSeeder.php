<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetLocation;
use App\Models\JobPosting;
use App\Models\JobApplication;
use App\Models\Customer;
use App\Models\Ticket;
use App\Models\LeaveRequest;
use Carbon\Carbon;

class ErpDashboardDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Asset Categories & Locations
        $catIT = AssetCategory::firstOrCreate(['name' => 'IT & Computing'], ['description' => 'Laptops, desktops, monitors, and peripherals']);
        $catNet = AssetCategory::firstOrCreate(['name' => 'Networking & Servers'], ['description' => 'Routers, switches, rack servers']);
        $catFurn = AssetCategory::firstOrCreate(['name' => 'Office Infrastructure'], ['description' => 'Ergonomic chairs, conference displays']);
        $catMobile = AssetCategory::firstOrCreate(['name' => 'Mobile & Telecom'], ['description' => 'Company smartphones and tablets']);

        $locHQ = AssetLocation::firstOrCreate(['name' => 'HQ - Main Office (Floor 3)'], ['address' => 'Victoria Island, Lagos']);
        $locDC = AssetLocation::firstOrCreate(['name' => 'HQ - Data Center (Server Room)'], ['address' => 'Floor 1 Server Hub']);
        $locAbuja = AssetLocation::firstOrCreate(['name' => 'Abuja Regional Branch'], ['address' => 'Central Business District, Abuja']);
        $locRemote = AssetLocation::firstOrCreate(['name' => 'Remote / Work From Home'], ['address' => 'Distributed Workforce']);

        // Pick some active users
        $users = User::where('is_active', 1)->take(15)->get();
        if ($users->isEmpty()) {
            $users = User::take(5)->get();
        }

        // 2. Add realistic Assets if count is low
        if (Asset::count() < 10) {
            $sampleAssets = [
                ['name' => 'MacBook Pro 16" M3 Max (36GB/1TB)', 'category_id' => $catIT->id, 'location_id' => $locHQ->id, 'cost' => 3800000, 'status' => 'Active', 'tag' => 'AST-MBP-001'],
                ['name' => 'Dell XPS 15 9530 (i9/32GB/1TB)', 'category_id' => $catIT->id, 'location_id' => $locHQ->id, 'cost' => 2450000, 'status' => 'Active', 'tag' => 'AST-XPS-002'],
                ['name' => 'Cisco Catalyst 9300 48-Port Switch', 'category_id' => $catNet->id, 'location_id' => $locDC->id, 'cost' => 5200000, 'status' => 'Active', 'tag' => 'AST-NET-003'],
                ['name' => 'Dell PowerEdge R750 2U Rack Server', 'category_id' => $catNet->id, 'location_id' => $locDC->id, 'cost' => 9800000, 'status' => 'Active', 'tag' => 'AST-SRV-004'],
                ['name' => 'Herman Miller Aeron Ergonomic Chair', 'category_id' => $catFurn->id, 'location_id' => $locHQ->id, 'cost' => 1250000, 'status' => 'Active', 'tag' => 'AST-FUR-005'],
                ['name' => 'Samsung 75" 4K Smart Interactive Board', 'category_id' => $catFurn->id, 'location_id' => $locHQ->id, 'cost' => 2900000, 'status' => 'Active', 'tag' => 'AST-DSP-006'],
                ['name' => 'HP LaserJet Enterprise MFP M630', 'category_id' => $catIT->id, 'location_id' => $locAbuja->id, 'cost' => 1850000, 'status' => 'Maintenance', 'tag' => 'AST-PRN-007'],
                ['name' => 'Lenovo ThinkPad X1 Carbon Gen 11', 'category_id' => $catIT->id, 'location_id' => $locRemote->id, 'cost' => 2100000, 'status' => 'Active', 'tag' => 'AST-TP-008'],
                ['name' => 'Apple iPad Pro 12.9" M2 (Cellular)', 'category_id' => $catMobile->id, 'location_id' => $locAbuja->id, 'cost' => 1450000, 'status' => 'Active', 'tag' => 'AST-TAB-009'],
                ['name' => 'Fortinet FortiGate 100F Firewall', 'category_id' => $catNet->id, 'location_id' => $locDC->id, 'cost' => 4300000, 'status' => 'Active', 'tag' => 'AST-SEC-010'],
                ['name' => 'MacBook Air 15" M2 (16GB/512GB)', 'category_id' => $catIT->id, 'location_id' => $locRemote->id, 'cost' => 1750000, 'status' => 'Active', 'tag' => 'AST-MBA-011'],
                ['name' => 'APC Smart-UPS RT 10kVA On-Line Power', 'category_id' => $catNet->id, 'location_id' => $locDC->id, 'cost' => 6400000, 'status' => 'Active', 'tag' => 'AST-PWR-012'],
            ];

            foreach ($sampleAssets as $idx => $assetData) {
                Asset::firstOrCreate(
                    ['asset_tag' => $assetData['tag']],
                    [
                        'name' => $assetData['name'],
                        'category_id' => $assetData['category_id'],
                        'location_id' => $assetData['location_id'],
                        'custodian_id' => $users->isNotEmpty() ? $users[$idx % $users->count()]->id : null,
                        'specifications' => 'Standard enterprise specifications and warranty package.',
                        'purchase_date' => Carbon::now()->subMonths(rand(2, 24))->format('Y-m-d'),
                        'purchase_cost' => $assetData['cost'],
                        'salvage_value' => $assetData['cost'] * 0.1,
                        'useful_life_years' => 4,
                        'depreciation_method' => 'straight_line',
                        'status' => $assetData['status'],
                    ]
                );
            }
        }

        // 3. Add Job Applications
        $jobs = JobPosting::all();
        if ($jobs->isNotEmpty() && JobApplication::count() < 10) {
            $applicants = [
                ['fn' => 'Chioma', 'ln' => 'Adeyemi', 'email' => 'chioma.adeyemi@example.com', 'phone' => '+234 803 111 2233', 'status' => 'Interviewing'],
                ['fn' => 'Babajide', 'ln' => 'Okonkwo', 'email' => 'b.okonkwo@example.com', 'phone' => '+234 802 444 5566', 'status' => 'In Review'],
                ['fn' => 'Amina', 'ln' => 'Bello', 'email' => 'amina.bello@example.com', 'phone' => '+234 818 777 8899', 'status' => 'Offered'],
                ['fn' => 'David', 'ln' => 'Chen', 'email' => 'david.chen@example.com', 'phone' => '+234 809 333 4455', 'status' => 'New'],
                ['fn' => 'Zainab', 'ln' => 'Ibrahim', 'email' => 'zainab.ibrahim@example.com', 'phone' => '+234 805 666 7788', 'status' => 'In Review'],
                ['fn' => 'Emeka', 'ln' => 'Nnamdi', 'email' => 'emeka.nnamdi@example.com', 'phone' => '+234 812 999 0011', 'status' => 'Interviewing'],
                ['fn' => 'Fatima', 'ln' => 'Mohammed', 'email' => 'fatima.m@example.com', 'phone' => '+234 807 222 3344', 'status' => 'New'],
                ['fn' => 'Olumide', 'ln' => 'Balogun', 'email' => 'olumide.b@example.com', 'phone' => '+234 816 555 6677', 'status' => 'Offered'],
            ];

            foreach ($applicants as $idx => $app) {
                $job = $jobs[$idx % $jobs->count()];
                JobApplication::firstOrCreate(
                    ['email' => $app['email']],
                    [
                        'job_posting_id' => $job->id,
                        'first_name' => $app['fn'],
                        'last_name' => $app['ln'],
                        'phone' => $app['phone'],
                        'resume_path' => 'resumes/sample_resume.pdf',
                        'cover_letter' => 'Excited to apply for this position and contribute to the enterprise roadmap.',
                        'status' => $app['status'],
                        'created_at' => Carbon::now()->subDays(rand(1, 20)),
                    ]
                );
            }
        }

        // 4. Add Leave Requests
        if ($users->count() >= 3 && LeaveRequest::count() < 6) {
            $leaveTypes = ['Annual Leave', 'Casual Leave', 'Sick Leave', 'Maternity Leave', 'Compassionate Leave'];
            $statuses = ['Pending', 'Approved', 'Approved', 'Pending', 'Rejected'];

            for ($i = 0; $i < 6; $i++) {
                $u = $users[$i % $users->count()];
                $start = Carbon::now()->addDays(rand(2, 30));
                $days = rand(2, 10);
                $end = (clone $start)->addDays($days);

                LeaveRequest::create([
                    'user_id' => $u->id,
                    'leave_type' => $leaveTypes[$i % count($leaveTypes)],
                    'start_date' => $start->format('Y-m-d'),
                    'end_date' => $end->format('Y-m-d'),
                    'total_days' => $days,
                    'reason' => 'Annual leave allocation and scheduled downtime.',
                    'status' => $statuses[$i % count($statuses)],
                    'created_at' => Carbon::now()->subDays(rand(1, 14)),
                ]);
            }
        }

        // 5. Add Helpdesk Support Tickets
        $customers = Customer::all();
        if ($customers->isEmpty()) {
            $customers = [
                Customer::create(['name' => 'Acme Global Logistics', 'email' => 'support@acmeglobal.com', 'phone' => '+234 801 000 1111']),
                Customer::create(['name' => 'Apex Financial Services', 'email' => 'ops@apexfinance.ng', 'phone' => '+234 802 000 2222']),
                Customer::create(['name' => 'Sterling Health Systems', 'email' => 'it@sterlinghealth.org', 'phone' => '+234 803 000 3333']),
            ];
            $customers = collect($customers);
        }

        if (Ticket::count() < 8) {
            $ticketDefs = [
                ['subj' => 'ERP API Integration Webhook Timeout', 'cat' => 'Technical', 'prio' => 'Urgent', 'stat' => 'Open'],
                ['subj' => 'Monthly Billing Invoice Reconciliation Discrepancy', 'cat' => 'Billing', 'prio' => 'High', 'stat' => 'In Progress'],
                ['subj' => 'Single Sign-On (SSO) SAML Certificate Renewal', 'cat' => 'Technical', 'prio' => 'High', 'stat' => 'In Progress'],
                ['subj' => 'User Role Permission Matrix Update for Procurement Team', 'cat' => 'Account', 'prio' => 'Medium', 'stat' => 'Resolved'],
                ['subj' => 'Asset Barcode Scanner Driver Sync Failure', 'cat' => 'Technical', 'prio' => 'Medium', 'stat' => 'Open'],
                ['subj' => 'Request for Export of Departmental Ledger Records (Q2)', 'cat' => 'General', 'prio' => 'Low', 'stat' => 'Resolved'],
                ['subj' => 'Warehouse Inventory Sync Lag Notice', 'cat' => 'Technical', 'prio' => 'High', 'stat' => 'In Progress'],
            ];

            foreach ($ticketDefs as $idx => $td) {
                $cust = $customers[$idx % $customers->count()];
                Ticket::firstOrCreate(
                    ['ticket_key' => 'TCK-' . (1000 + $idx)],
                    [
                        'customer_id' => $cust->id,
                        'subject' => $td['subj'],
                        'description' => 'Detailed ticket inquiry regarding ' . $td['subj'] . '. Client requests prompt assessment.',
                        'status' => $td['stat'],
                        'priority' => $td['prio'],
                        'category' => $td['cat'],
                        'channel' => 'Portal',
                        'assigned_to_id' => $users->isNotEmpty() ? $users[$idx % $users->count()]->id : null,
                        'sla_deadline' => Carbon::now()->addHours(rand(4, 48)),
                        'created_at' => Carbon::now()->subDays(rand(1, 10)),
                    ]
                );
            }
        }
    }
}
