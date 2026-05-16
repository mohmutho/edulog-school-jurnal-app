<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PasswordResetRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Email tidak terdaftar dalam sistem.',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();
        
        if ($user) {
            $user->is_requesting_password_reset = true;
            $user->save();
        }

        return back()->with('status', 'Permintaan reset password telah dikirim ke Admin Kurikulum.');
    }
}