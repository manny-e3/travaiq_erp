<?php

namespace App\Services;

use App\Models\Department;

class DepartmentService
{
    public function getAllWithUserCount()
    {
        return Department::withCount('users')->get();
    }

    public function createDepartment(array $data): Department
    {
        return Department::create($data);
    }

    public function updateDepartment(Department $department, array $data): bool
    {
        return $department->update($data);
    }

    public function deleteDepartment(Department $department): bool
    {
        if ($department->users()->exists()) {
            return false;
        }

        return $department->delete();
    }
}
