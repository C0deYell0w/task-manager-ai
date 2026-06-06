<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    public function users(Request $request)
    {
        Gate::authorize('manage-users');

        $users = User::paginate(15);

        return Inertia::render('Admin/Users', [
            'users' => $users
        ]);
    }
}
