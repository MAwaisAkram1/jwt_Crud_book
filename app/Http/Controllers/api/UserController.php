<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Models\UserToken;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\UserConfirmationMail;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Jobs\SendConfirmationEmail;

class UserController extends Controller
{
    public function register(Request $request) {
        $data = $request->all();

        $token = Str::random(60);
        $data['confirmation_token'] = $token;
        $data['token_expiration'] =  now()->addMinutes(5);
        $user = User::registerUser($data);

        SendConfirmationEmail::dispatch($user, $token);
        return response()->json([
            'message' => 'User registration Pending. Please check your email to confirm your registration.',
            'user' => $user,
        ]);
    }

    public function confirm($token) {
        $user = User::where('confirmation_token', $token)->where('token_expiration', '>', now())->first();
        if (!$user) {
            return response()->json([
               'message' => 'Invalid or Expired token',
            ], 400);
        }

        $user->email_verified_at=now();
        $user->confirmation_token=null;
        $user->token_expiration=null;
        $user->save();

        return response()->json(['message' => 'Email confirmed successfully']);
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
            // Check if email is verified
            if (is_null($user->email_verified_at)) {
                return response()->json([
                    'message' => 'Email not verified',
                ], 403); // 403 Forbidden
            }
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
