<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
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
         'expires_in' => auth()->factory()->getTTL() * 60
//        'user' => auth('api')->user(),

      ],201);
    }

    public function GetProfile(Request $request)
    {
        $user = auth('api')->user()->load('wallet');

        if (!$user) {
            return response()->json([
                'status' => 401,
                'message' => "Unauthorized"
            ], 401);
        }

        // إضافة رابط الصورة الكامل
        $user->image = $user->image ? asset('storage/' . $user->image) : null;

        return response()->json([
            'status' => 200,
            'message' => "User retrieved successfully",
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'image' => $user->image_url, // Using the accessor
                'points' => $user->wallet?->points ?? 0, // If no wallet, return 0
            ]
        ]);
    }


    public  function EditProfile(Request $request)
    {
// استرجاع المستخدم من الـ JWT
        $user = JWTAuth::user();

        // التحقق من البيانات المدخلة
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'phone' => 'sometimes|regex:/^01[0125][0-9]{8}$/',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // تحديث البيانات إذا كانت موجودة في الطلب
        if ($request->has('name')) $user->name = $request->name;
        if ($request->has('email')) $user->email = $request->email;
        if ($request->has('phone')) $user->phone = $request->phone;

        // تحديث الصورة الشخصية
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إن وجدت
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }

            // حفظ الصورة الجديدة
            $path = $request->file('image')->store('profile_images', 'public');
            $user->image = $path;
        }

        // حفظ التغييرات
        $user->save();
        $user->image = $user->image ? asset('storage/' . $user->image) : null;
        return response()->json([
            'message' => 'Profile updated successfully!',
            'user' => $user
        ], 200);
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
