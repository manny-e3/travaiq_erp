<?php

namespace App\Services;

use App\Models\Designation;

class DesignationService
{
    public function getAllWithUserCount()
    {
        return Designation::withCount('users')->get();
    }

    public function createDesignation(array $data): Designation
    {
        return Designation::create($data);
    }

    public function updateDesignation(Designation $designation, array $data): bool
    {
        return $designation->update($data);
    }

    public function deleteDesignation(Designation $designation): bool
    {
        if ($designation->users()->exists()) {
            return false;
        }

        return $designation->delete();
    }
}
