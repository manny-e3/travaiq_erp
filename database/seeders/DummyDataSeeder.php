<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Human Resources', 'description' => 'Handles employee relations and benefits'],
            ['name' => 'Marketing', 'description' => 'Manages brand and campaigns'],
            ['name' => 'Sales', 'description' => 'Drives revenue and customer acquisition'],
            ['name' => 'Engineering', 'description' => 'Develops and maintains software products'],
            ['name' => 'Customer Support', 'description' => 'Assists clients with issues'],
        ];

        $deptIds = [];
        foreach ($departments as $deptData) {
            $dept = Department::firstOrCreate($deptData);
            $deptIds[] = $dept->id;
        }

        $userRole = Role::where('name', 'user')->first() ?? Role::findOrCreate('user');
        
        $jobTitles = ['Manager', 'Specialist', 'Coordinator', 'Analyst', 'Director', 'Consultant'];

        for ($i = 1; $i <= 30; $i++) {
            $user = User::factory()->create([
                'department_id' => $deptIds[array_rand($deptIds)],
                'job_title' => $jobTitles[array_rand($jobTitles)],
                'employee_id' => 'EMP-' . strtoupper(Str::random(6)),
                'phone' => '555-' . rand(100, 999) . '-' . rand(1000, 9999),
                'is_active' => rand(1, 100) > 15, // 85% active
            ]);

            $user->assignRole($userRole);
        }
    }
}
