<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Fetch user from the 'login' table
        $user = DB::table('login')->where('username', $request->username)->first();

        // Check if the user exists and the password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // If credentials are correct, return a success response
        return response()->json([
            'message' => 'Login successful',
            'user' => [
                'login_id' => $user->login_id,
                'username' => $user->username,
            ],
        ], 200);
    }
}
