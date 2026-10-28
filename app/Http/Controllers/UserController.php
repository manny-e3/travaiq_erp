<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Services\UserService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $users = $this->userService->getUsers();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        $departments = Department::all();
        $designations = \App\Models\Designation::all();
        return view('admin.users.create', compact('roles', 'departments', 'designations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'role'          => ['required', 'exists:roles,name'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'employee_id'   => ['nullable', 'string', 'max:255', 'unique:'.User::class],
            'phone'         => ['nullable', 'string', 'max:255'],
            'designation_id'=> ['nullable', 'exists:designations,id'],
            'job_title'     => ['nullable', 'string', 'max:255'],
            'avatar'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);

        $this->userService->createUser($data);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully and login details sent.');
    }

    public function show(User $user)
    {
        $user->load(['roles', 'department', 'loginHistories' => function($query) {
            $query->latest('login_at')->take(50);
        }]);
        
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $departments = Department::all();
        $designations = \App\Models\Designation::all();
        return view('admin.users.edit', compact('user', 'roles', 'departments', 'designations'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role'          => ['required', 'exists:roles,name'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'employee_id'   => ['nullable', 'string', 'max:255', 'unique:users,employee_id,'.$user->id],
            'phone'         => ['nullable', 'string', 'max:255'],
            'designation_id'=> ['nullable', 'exists:designations,id'],
            'job_title'     => ['nullable', 'string', 'max:255'],
            'avatar'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $data['password'] = $request->password;
        }

        $this->userService->updateUser($user, $data);

        return redirect()->route('admin.users.show', $user->id)->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($this->userService->deleteUser($user, auth()->id())) {
            return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
        }
        
        return redirect()->back()->with('error', 'You cannot delete yourself.');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'activated' : 'deactivated';

        return redirect()->back()->with('success', "User account {$status} successfully.");
    }

    public function importForm()
    {
        return view('admin.users.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $filePath = $request->file('file')->getRealPath();
        
        try {
            $count = $this->userService->importUsersFromCsv($filePath);
            return redirect()->route('admin.users.index')->with('success', "Successfully imported {$count} users.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing users: ' . $e->getMessage());
        }
    }
}
