<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Designation;
use App\Models\Department;
use Spatie\Permission\Models\Role;

class DesignationManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::findOrCreate('user');
        Role::findOrCreate('admin');
    }

    public function test_admin_can_manage_designation_crud(): void
    {
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $adminUser->assignRole('admin');

        // Create Designation
        $response = $this->actingAs($adminUser)
            ->post(route('admin.designations.store'), [
                'name' => 'Senior Developer',
                'description' => 'Senior Developer Role Description'
            ]);

        $response->assertRedirect(route('admin.designations.index'));
        $this->assertDatabaseHas('designations', [
            'name' => 'Senior Developer',
            'description' => 'Senior Developer Role Description'
        ]);

        $designation = Designation::where('name', 'Senior Developer')->first();

        // Update Designation
        $response = $this->actingAs($adminUser)
            ->put(route('admin.designations.update', $designation->id), [
                'name' => 'Lead Developer',
                'description' => 'Updated Description'
            ]);

        $response->assertRedirect(route('admin.designations.index'));
        $this->assertDatabaseHas('designations', [
            'id' => $designation->id,
            'name' => 'Lead Developer',
            'description' => 'Updated Description'
        ]);

        // Delete Designation
        $response = $this->actingAs($adminUser)
            ->delete(route('admin.designations.destroy', $designation->id));

        $response->assertRedirect(route('admin.designations.index'));
        $this->assertDatabaseMissing('designations', [
            'id' => $designation->id
        ]);
    }

    public function test_user_creation_with_designation(): void
    {
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $adminUser->assignRole('admin');

        $designation = Designation::create([
            'name' => 'QA Engineer',
            'description' => 'Quality Assurance'
        ]);

        $response = $this->actingAs($adminUser)
            ->post(route('admin.users.store'), [
                'name' => 'Bob Test',
                'email' => 'bob@test.com',
                'role' => 'user',
                'designation_id' => $designation->id,
                'password' => 'SecurePass123!',
                'password_confirmation' => 'SecurePass123!',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'Bob Test',
            'email' => 'bob@test.com',
            'designation_id' => $designation->id,
            'job_title' => 'QA Engineer',
        ]);
    }

    public function test_user_update_with_designation(): void
    {
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $adminUser->assignRole('admin');

        $user = User::create([
            'name' => 'Alice Test',
            'email' => 'alice@test.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $user->assignRole('user');

        $designation = Designation::create([
            'name' => 'DevOps Engineer',
            'description' => 'DevOps'
        ]);

        $response = $this->actingAs($adminUser)
            ->put(route('admin.users.update', $user->id), [
                'name' => 'Alice Test',
                'email' => 'alice@test.com',
                'role' => 'user',
                'designation_id' => $designation->id,
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.users.show', $user->id));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'designation_id' => $designation->id,
            'job_title' => 'DevOps Engineer',
        ]);
    }

    public function test_employee_profile_update_with_designation(): void
    {
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $adminUser->assignRole('admin');

        $user = User::create([
            'name' => 'Dave Test',
            'email' => 'dave@test.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $user->assignRole('user');

        $designation = Designation::create([
            'name' => 'Product Manager',
            'description' => 'Product Management'
        ]);

        $response = $this->actingAs($adminUser)
            ->post(route('admin.hr.employees.updateProfile', $user->id), [
                'name' => 'Dave Test Updated',
                'designation_id' => $designation->id,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Dave Test Updated',
            'designation_id' => $designation->id,
            'job_title' => 'Product Manager',
        ]);
    }

    public function test_employment_history_logs_with_designation(): void
    {
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $adminUser->assignRole('admin');

        $user = User::create([
            'name' => 'Eve Test',
            'email' => 'eve@test.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $user->assignRole('user');

        $designation = Designation::create([
            'name' => 'Support Lead',
            'description' => 'Customer Support'
        ]);

        $response = $this->actingAs($adminUser)
            ->post(route('admin.hr.employees.addHistory', $user->id), [
                'designation_id' => $designation->id,
                'start_date' => '2026-06-01',
                'change_reason' => 'Promotion',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('employment_histories', [
            'user_id' => $user->id,
            'designation_id' => $designation->id,
            'job_title' => 'Support Lead',
            'change_reason' => 'Promotion',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'designation_id' => $designation->id,
            'job_title' => 'Support Lead',
        ]);
    }
}
