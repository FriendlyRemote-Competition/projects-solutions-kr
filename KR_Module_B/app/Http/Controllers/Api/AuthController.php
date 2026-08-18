<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)
            ->where('is_active', true)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = Str::random(60);

        $user->update([
            'api_token' => $token
        ]);

        return response()->json([
            "data" => [
                "token" => $user->api_token,
                "user" => [
                    "email" => $user->email,
                    "name" => $user->name,
                    "role" => $user->role
                ]
            ]
        ]);
    }
}
