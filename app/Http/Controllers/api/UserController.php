<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Models\UserToken;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
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
                'message' => 'User Logged in Successfully',
                'token'=>$token,
            ], 201);
            // return Response::success('User Login Success', 201);

        } catch (JWTException $e) {
            return response()->json([
               'message' => 'Failed to create token',
            ], 500);
        }
    }

    public function refresh(Request $request) {
        try {
            $token = JWTAuth::getToken();
            if(!$token) {
                return response()->json([
                    'message' =>'token not found',
                ], 401);
            }
            $newToken = JWTAuth::refresh($token);
            $user = JWTAuth::setToken($newToken)->toUser();

            UserToken::where('token', $token)->delete();
            UserToken::create(['user_id' => $user->id, 'token' => $newToken]);
            return response()->json([
                'message' => 'Token Refresh successfully',
                'token' => $newToken,
            ], 201);
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Failed to refresh token, please try again',
            ], 401);
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
