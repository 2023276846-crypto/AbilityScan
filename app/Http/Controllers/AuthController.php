<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PwdProfile;
use App\Models\EmployerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Register PWD
    public function registerPWD(Request $request)
    {
        $request->validate([
            'name'       => 'required|string',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|min:6',
            'full_name'  => 'required|string',
            'oku_number' => 'required|string|unique:pwd_profiles',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'pwd',
        ]);

        PwdProfile::create([
            'user_id'    => $user->id,
            'full_name'  => $request->full_name,
            'oku_number' => $request->oku_number,
            'status'     => 'pending',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'token'   => $token,
            'role'    => 'pwd'
        ]);
    }

    // Register Employer
    public function registerEmployer(Request $request)
    {
        $request->validate([
            'name'         => 'required|string',
            'email'        => 'required|email|unique:users',
            'password'     => 'required|min:6',
            'company_name' => 'required|string',
            'ssm_number'   => 'required|string|unique:employer_profiles',
            'company_address' => 'required|string',
            'industry_type'   => 'required|string',
            'contact_number'  => 'required|string',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'employer',
        ]);

        EmployerProfile::create([
            'user_id'         => $user->id,
            'company_name'    => $request->company_name,
            'company_address' => $request->company_address,
            'industry_type'   => $request->industry_type,
            'ssm_number'      => $request->ssm_number,
            'contact_number'  => $request->contact_number,
            'company_email'   => $request->email,
            'status'          => 'pending',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'token'   => $token,
            'role'    => 'employer'
        ]);
    }

    // Login
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
            'role'     => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        if ($user->role !== $request->role) {
            return response()->json([
                'message' => 'Invalid role for this account'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token'   => $token,
            'role'    => $user->role,
            'user'    => $user
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}