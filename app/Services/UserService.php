<?php

namespace App\Services;

use App\Models\User;
use App\Models\Department;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserWelcomeMail;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    /**
     * Get all users with their roles and department.
     */
    public function getUsers(): Collection
    {
        return User::with(['roles', 'department'])->get();
    }

    /**
     * Create a new user, assign a role, and send a welcome email.
     */
    public function createUser(array $data): User
    {
        $password = Str::random(12);

        $user = User::create([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'password'      => Hash::make($password),
            'department_id' => $data['department_id'] ?? null,
            'employee_id'   => $data['employee_id'] ?? null,
            'phone'         => $data['phone'] ?? null,
            'designation_id'=> $data['designation_id'] ?? null,
            'job_title'     => isset($data['designation_id']) ? (\App\Models\Designation::find($data['designation_id'])?->name) : ($data['job_title'] ?? null),
            'is_active'     => $data['is_active'] ?? true,
        ]);

        if (isset($data['avatar'])) {
            $path = $data['avatar']->store('avatars', 'public');
            $user->update(['avatar' => $path]);
        }

        $user->assignRole($data['role']);

        Mail::to($user->email)->send(new UserWelcomeMail($user, $password));

        return $user;
    }

    /**
     * Update an existing user.
     */
    public function updateUser(User $user, array $data): void
    {
        $updateData = [
            'name'          => $data['name'],
            'email'         => $data['email'],
            'department_id' => $data['department_id'] ?? null,
            'employee_id'   => $data['employee_id'] ?? null,
            'phone'         => $data['phone'] ?? null,
            'designation_id'=> $data['designation_id'] ?? null,
            'job_title'     => isset($data['designation_id']) ? (\App\Models\Designation::find($data['designation_id'])?->name) : ($data['job_title'] ?? null),
            'is_active'     => $data['is_active'] ?? $user->is_active,
        ];

        if (isset($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        if (isset($data['avatar'])) {
            $path = $data['avatar']->store('avatars', 'public');
            $user->update(['avatar' => $path]);
        }

        $user->syncRoles([$data['role']]);
    }

    /**
     * Delete a user.
     */
    public function deleteUser(User $user, int $currentUserId): bool
    {
        if ($user->id === $currentUserId) {
            return false;
        }

        return (bool) $user->delete();
    }

    /**
     * Import users from CSV file.
     */
    public function importUsersFromCsv($filePath): int
    {
        $file = fopen($filePath, 'r');
        $headers = fgetcsv($file);
        $importedCount = 0;

        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($headers, $row);

            // Basic validation
            if (empty($data['name']) || empty($data['email']) || empty($data['role_name'])) {
                continue; // Skip invalid rows
            }

            // Check if user already exists
            if (User::where('email', $data['email'])->exists()) {
                continue;
            }

            $departmentId = null;
            if (!empty($data['department_name'])) {
                $department = Department::firstOrCreate(['name' => $data['department_name']]);
                $departmentId = $department->id;
            }

            $role = Role::where('name', strtolower($data['role_name']))->first();
            if (!$role) {
                // If role doesn't exist, default to 'user' or create it
                $role = Role::firstOrCreate(['name' => strtolower($data['role_name'])]);
            }

            $designationId = null;
            if (!empty($data['job_title'])) {
                $designation = \App\Models\Designation::firstOrCreate(['name' => trim($data['job_title'])]);
                $designationId = $designation->id;
            }

            $userData = [
                'name'          => $data['name'],
                'email'         => $data['email'],
                'role'          => $role->name,
                'department_id' => $departmentId,
                'employee_id'   => $data['employee_id'] ?? null,
                'phone'         => $data['phone'] ?? null,
                'designation_id'=> $designationId,
                'job_title'     => $data['job_title'] ?? null,
            ];

            $this->createUser($userData);
            $importedCount++;
        }

        fclose($file);
        return $importedCount;
    }

    // Role methods ...
    public function getRoles(): Collection { return Role::withCount('users')->get(); }
    public function createRole(string $name): Role { return Role::create(['name' => $name]); }
    public function updateRole(Role $role, string $name, array $permissions): void {
        $role->update(['name' => $name]);
        $role->syncPermissions($permissions);
    }
    public function deleteRole(Role $role): bool {
        if (in_array($role->name, ['admin', 'author'])) return false;
        return (bool) $role->delete();
    }
}
