<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobPosting;
use App\Models\User;
use App\Models\Department;
use Carbon\Carbon;

class JobPostingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Try to get a user, if not create one
        $user = User::first() ?? User::factory()->create();

        // Try to get a department, if not create one
        $department = Department::first();
        if (!$department) {
            $department = Department::create([
                'name' => 'Engineering',
                'description' => 'Software Development'
            ]);
        }

        JobPosting::create([
            'title' => 'Senior PHP Developer',
            'description' => 'We are looking for an experienced Senior PHP Developer to join our core product team. You will be responsible for building high-quality, scalable web applications using Laravel. The ideal candidate has deep knowledge of modern PHP, RESTful APIs, and relational databases. Experience with Vue.js or React is a plus.',
            'department_id' => $department->id,
            'location' => 'Remote / Denver, CO',
            'employment_type' => 'Full-time',
            'status' => 'Open',
            'closing_date' => Carbon::now()->addDays(30),
            'created_by' => $user->id,
        ]);

        JobPosting::create([
            'title' => 'Product Designer (UX/UI)',
            'description' => 'BPA Enterprise is seeking a talented Product Designer to help shape the future of our enterprise software solutions. You will work closely with product managers and engineers to design intuitive, beautiful, and functional interfaces. A strong portfolio demonstrating your problem-solving process and high-fidelity design skills is required.',
            'department_id' => $department->id,
            'location' => 'New York, NY',
            'employment_type' => 'Full-time',
            'status' => 'Open',
            'closing_date' => Carbon::now()->addDays(15),
            'created_by' => $user->id,
        ]);

        JobPosting::create([
            'title' => 'Customer Success Manager',
            'description' => 'Join our Customer Success team to ensure our enterprise clients achieve their business goals using our platform. You will manage a portfolio of accounts, drive product adoption, and serve as the primary point of contact for escalations. Strong communication skills and a background in B2B SaaS are essential.',
            'department_id' => $department->id,
            'location' => 'Chicago, IL',
            'employment_type' => 'Contract',
            'status' => 'Closed',
            'closing_date' => Carbon::now()->subDays(5),
            'created_by' => $user->id,
        ]);

        JobPosting::create([
            'title' => 'Enterprise Sales Executive',
            'description' => 'We are seeking a high-performing Enterprise Sales Executive to help us meet our customer acquisition and revenue growth targets by keeping our company competitive and innovative. You will be responsible for maximizing our sales team potential, crafting sales plans and justifying those to plans to the upper management.',
            'department_id' => $department->id,
            'location' => 'San Francisco, CA',
            'employment_type' => 'Full-time',
            'status' => 'Open',
            'closing_date' => Carbon::now()->addDays(20),
            'created_by' => $user->id,
        ]);

        JobPosting::create([
            'title' => 'Customer Care Representative',
            'description' => 'As a Customer Care Representative, you will be the first point of contact for our customers, providing exceptional support and resolving inquiries efficiently. You should have excellent communication skills, empathy, and a passion for helping others. Previous experience in a high-volume call center or support desk is highly desired.',
            'department_id' => $department->id,
            'location' => 'Austin, TX',
            'employment_type' => 'Full-time',
            'status' => 'Open',
            'closing_date' => Carbon::now()->addDays(45),
            'created_by' => $user->id,
        ]);
    }
}
