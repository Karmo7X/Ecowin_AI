<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'status' => 201,
            'message' => 'User registered successfully',
//            'user' => $user,
            'token'=>$token,
        ], 201);
    }
    public function login(LoginRequest $request)
    {

    $credentials = $request->only('email', 'password');

    try {
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid Credentials'], 401);
        }
    } catch (JWTException $e) {
        return response()->json(['error' => 'Could not create token'], 500);
    }

     return response()->json([
         'status' => 201,
         'message'=> "user logged in successfuly",
        'token' => $token,
//        'user' => auth('api')->user(),

      ],201);
    }

    public function GetProfile(Request $request)
    {
        return response()->json([
            'status' => 200,
            'message'=> "user return successfully",
            'user' => auth('api')->user()
        ]);
    }
public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'status' => 200,
                'message' => 'User logged out successfully',
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Failed to logout, please try again.',
            ], 500);
        }
    }
}
