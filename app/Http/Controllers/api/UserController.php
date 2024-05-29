<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

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
        $user = User::loginUser($request->all());
        if (!$user) {
            return response()->json([
                'message' =>'Invalid Credentials',
            ], 401);
        }
        $token = JwtAuth::fromUser($user);
        return response()->json([
            'message'=> 'User login successful',
            'token'=>$token,
        ]);
    }
}
