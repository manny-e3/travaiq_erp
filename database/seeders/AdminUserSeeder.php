<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::findOrCreate('admin');

        $dept = Department::firstOrCreate(['name' => 'IT Department', 'description' => 'Information Technology']);

        $user = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'department_id' => $dept->id,
                'is_active' => true,
            ]
        );

        $user->assignRole($adminRole);
    }
}
