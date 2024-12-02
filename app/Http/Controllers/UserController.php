<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function register(Request $request)
    {
        Log::info('Register method called.', ['data' => $request->all()]);

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:8',
                'role' => 'required|in:student,teacher,admin',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed.', $e->errors());
            return response()->json(['errors' => $e->errors()], 422);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        Log::info('User created successfully.', ['user_id' => $user->id]);

        return response()->json(['token' => JWTAuth::fromUser($user)], 201);
    }

    public function login(Request $request)
    {
        Log::info('Login method called.', ['email' => $request->email]);

        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            Log::warning('Unauthorized login attempt.', ['email' => $request->email]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        Log::info('Login successful.', ['email' => $request->email]);

        return response()->json(compact('token'));
    }

    public function profile()
    {
        try {
            Log::info('Profile method called.', ['user_id' => auth()->id()]);
            $user = auth()->user();
            Log::info('User retrieved successfully.', ['user' => $user]);

            return response()->json($user);
        } catch (\Exception $e) {
            Log::error('Error retrieving profile.', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Unable to retrieve profile'], 500);
        }
    }
}
