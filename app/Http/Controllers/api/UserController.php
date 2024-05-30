<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserToken;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function register(Request $request) {

        $user = User::registerUser($request->all());
        // $token = JwtAuth::fromUser($user);
        return response()->json([
            'message' => 'User registration successful',
            'user' => $user,
            // 'token'=> $token,
        ]);
    }

    public function login(Request $request) {
        $userCredentials = $request->only('email', 'password');
        try {
            if (!$token = JwtAuth::attempt($userCredentials)){
                return response()->json([
                    'message' =>'Invalid Credentials',
                ], 401);
            }
            $user = JWTAuth::user();
            // Store the token
            UserToken::create(['user_id' => $user->id, 'token' => $token]);
            return response()->json([
                'message'=> 'User login successful',
                'token'=>$token,
            ], 201);

        } catch (JWTException $e) {
            return response()->json([
               'message' => 'Failed to create token',
            ], 500);
        }
    }

    public function logout(Request $request) {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::invalidate($token);
            UserToken::where('token', $token)->delete();
            return response()->json([
                'message' => 'User logout Successfully'
            ], 201);
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Failed to logout, please try again',
            ], 401);
        }
    }
}
