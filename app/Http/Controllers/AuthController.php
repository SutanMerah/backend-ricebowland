<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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
}