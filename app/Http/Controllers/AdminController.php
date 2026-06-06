<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    public function __construct(protected UserService $userService) {}

    /**
     * Display a paginated list of users for administration.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function users(Request $request)
    {
        Gate::authorize('manage-users');

        $users = $this->userService->getPaginatedUsers(15);

        return Inertia::render('Admin/Users', [
            'users' => \App\Http\Resources\UserResource::collection($users)
        ]);
    }
}
