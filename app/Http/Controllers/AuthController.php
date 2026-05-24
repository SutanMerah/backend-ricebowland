<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $req)
    {
        $user = User::create([
            'name' => $req->name,
            'email' => $req->email,
            'password' => Hash::make($req->password),
            'role' => $req->role
        ]);

        return response()->json($user);
    }

    public function login(Request $req)
    {
        if (!Auth::attempt($req->only('email','password'))) {
            return response()->json(['message' => 'Login gagal'], 401);
        }

        return response()->json(Auth::user());
    }

    public function forgotPassword(Request $req)
    {
        $req->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink($req->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Reset password link sent to your email.'], 200);
        }

        return response()->json(['message' => 'Unable to send reset password link.'], 400);
    }

    public function resetPassword(Request $req)
    {
        $req->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $req->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password has been reset successfully.'], 200);
        }

        return response()->json(['message' => 'Invalid or expired token.'], 422);
    }
}
