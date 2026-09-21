<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ADMIN
    public function adminRegister(StoreUserRequest $request) {
        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'admin'
        ]);

        return response()->json([
            'message' => 'Registration Successfull',
            'user' => new UserResource($user)
        ], 201);
    }

    public function adminLogin(LoginUserRequest $request) {
        $admin = User::where('username', $request->username)
                    ->where('role', 'admin')
                    ->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'message' => 'Invalid Credentials'
            ], 401);
        }

        $token = $admin->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login Successful',
            'token' => $token,
            'admin' => new UserResource($admin)
        ], 200);
    }

    public function adminLogout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout Successful'
        ], 200);
    }




    // USER
    public function userRegister(StoreUserRequest $request) {
        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'user'
        ]);

        return response()->json([
            'message' => 'Registration Successfull',
            'user' => new UserResource($user)
        ], 201);
    }

    public function userLogin(LoginUserRequest $request) {
        $user = User::where('username', $request->username)
                ->where('role', 'user')
                ->first();
        
        if (!$user || Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => "Invalid Credentials"
            ], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login Successfully',
            'token' => $token,
            'user' => new UserResource($user)
        ], 200);
    }

    public function userLogout(Request $request) {
        $request()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout Successfully'
        ],200);
    }
}

