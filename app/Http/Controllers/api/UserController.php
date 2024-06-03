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
        return Response::success("User registration Pending. Please check your email to confirm your registration.", 200);
    }

    public function confirm($token) {
        $user = User::where('confirmation_token', $token)->where('token_expiration', '>', now())->first();
        if (!$user) {
            return Response::fail("Invalid or Expired token", 400);
        }

        $user->email_verified_at=now();
        $user->confirmation_token=null;
        $user->token_expiration=null;
        $user->save();

        return Response::success("Your Email is Verified", 201);
    }

    public function login(Request $request) {
        $userCredentials = $request->only('email', 'password');
        $remember_me = $request->input('remember_me', false);
        try {
            $defaultTTL = config('jwt.ttl');
            if ($remember_me) {
                $extendTTL = 2;
                JWTAuth::factory()->setTTL($extendTTL);
            } else {
                JWTAuth::factory()->setTTL($defaultTTL);
            }

            if (!$token = JwtAuth::attempt($userCredentials)){
                return Response::fail("Invalid Credentials", 401);
            }

            $user = JWTAuth::user();
            // Check if email is verified
            if (is_null($user->email_verified_at)) {
                return Response::fail("Email Not Verified", 403);
            }
            // Store the token
            UserToken::create(['user_id' => $user->id, 'token' => $token]);

            $expire_in = $remember_me ? $extendTTL * 60 : $defaultTTL;
            $response = [
                'message' => 'User Logged In Successfully',
                'token' => $token,
                'expire_in' => $expire_in,
            ];
            return Response::success($response, 200);

        } catch (JWTException $e) {
            return Response::fail("Failed to create Token", 500);
        }
    }

    public function refresh(Request $request) {
        try {
            $token = JWTAuth::getToken();
            if(!$token) {
                return Response::fail("Token not Found", 401);
            }
            $newToken = JWTAuth::refresh($token);
            $user = JWTAuth::setToken($newToken)->toUser();

            UserToken::where('token', $token)->delete();
            UserToken::create(['user_id' => $user->id, 'token' => $newToken]);
            $response = [
                'message' => 'Your Token is refreshed',
                'token' => $newToken,
            ];
            return Response::success($response, 200);

        } catch (JWTException $e) {
            return Response::fail("Failed to Create Token, plz try again", 401);
        }
    }

    public function logout(Request $request) {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::invalidate($token);
            UserToken::where('token', $token)->delete();
            return Response::success("Your logout Successfully", 200);

        } catch (JWTException $e) {
            return Response::fail("Failed to logout, plz tyr again", 401);
        }
    }
}
