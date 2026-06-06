<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    /**
     * Get users for the task assignment dropdown (only if admin).
     *
     * @return \Illuminate\Database\Eloquent\Collection|array
     */
    public function getAdminDropdownUsers()
    {
        return auth()->check() && auth()->user()->isAdmin() 
            ? User::select('id', 'name')->get() 
            : [];
    }

    /**
     * Get a paginated list of users for the admin dashboard.
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginatedUsers(int $perPage = 15)
    {
        return User::select(['id', 'name', 'email', 'role', 'created_at'])
            ->latest()
            ->paginate($perPage);
    }
}
