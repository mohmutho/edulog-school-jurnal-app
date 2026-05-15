<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserResetController extends Controller
{
    public function index()
    {
        $users = User::where('role', '!=', 'administrator')
            ->orderBy('is_requesting_password_reset', 'desc')
            ->orderBy('name', 'asc')
            ->get();

        return Inertia::render('Admin/MasterData/UserReset/Index', [
            'users' => $users
        ]);
    }

    public function reset(User $user)
    {
        $user->password = Hash::make('smansa123');
        $user->is_requesting_password_reset = false;
        $user->save();

        return back()->with('status', 'Password berhasil direset menjadi smansa123');
    }
}
