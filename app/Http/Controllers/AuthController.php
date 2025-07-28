<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Enums\Role;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // Register (User only)
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone'    => 'required|string|max:20',
            'address'  => 'required|string|max:255',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone'    => $validated['phone'],
            'address'  => $validated['address'],
            'role'     => Role::USER,
        ]);

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil!', // Pesan opsional untuk frontend
            'data' => [ // <-- TAMBAHKAN WRAPPER 'data' INI
                'user'  => $user,
                'token' => $token,
            ]
        ], 201); // Status 201 Created
    }

    // Login for User
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Email atau Password Salah!'], 401); // Pesan lebih user-friendly
        }

        $user = Auth::user();

        if ($user->role->value !== Role::USER->value) {
            Auth::logout();
            return response()->json(['message' => 'Akun ini bukan akun pengguna biasa.'], 403);
        }

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil!', // Pesan opsional untuk frontend
            'data' => [
                'user' => $user,
                'token' => $token,
            ]
        ]); // Status default 200 OK
    }

    // Login for Admin
    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Email atau Password Salah!'], 401); // Pesan lebih user-friendly
        }

        $user = Auth::user();

        if ($user->role->value !== Role::ADMIN->value) {
            Auth::logout();
            return response()->json(['message' => 'Akun ini bukan akun administrator.'], 403);
        }

        $token = $user->createToken('adminAuthToken')->plainTextToken;

        return response()->json([
            'message' => 'Login Administrator berhasil!',
            'data' => [
                'user' => $user,
                'token' => $token,
            ]
        ]); // Status default 200 OK
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Berhasil logout.']);
    }

    // Get current user
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
