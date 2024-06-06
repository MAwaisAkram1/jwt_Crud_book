<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Models\UserToken;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Mail\UserConfirmationMail;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Jobs\SendConfirmationEmail;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    /*
    |   Register the user request from HTTP request in-coming data for use registration
    |   generate random string for the url generation address to verify the user
    |   sendConfirmationEmail to send the mail to the user email address it will dipatch
    |   to the jobs to where it will then send the mail to to the user.
    */
    public function register(Request $request) {
        $data = $request->all();

        $token = Str::random(60);
        $data['confirmation_token'] = $token;
        $data['token_expiration'] =  now()->addMinutes(5);


        $signedURL = URL::temporarySignedRoute(
            'confirm',
            $data['token_expiration'],
            ['token' => $token]
        );
        $user = User::registerUser($data);

        SendConfirmationEmail::dispatch($user, $signedURL);
        return Response::success("User registration Pending. Please check your email to confirm your registration.", 200);
    }

    /*
    |   after the mail sent to the user email address it will be give the time for the user to register
    |   the time duration will let the user ti verify with in the time or link will be expired.
    */

    public function confirm($token) {
        if (!$request->hasValidSignature()){
            return Response::fail("Invalid or Expired Signature", 400);
        }

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

    /*
    |   login feature to let the user login to the application after the authentication of the user
    |   remember me the feature will let the user to login into the application for longer duration
    |   if the remember me is not set then the user will be logged out after 30 Minutes of inactivity.
    */
    public function login(Request $request) {
        $userCredentials = $request->only('email', 'password');
        $remember_me = $request->input('remember_me', false);
        try {
            // default duration of token expiration
            $defaultTTL = config('jwt.ttl');
            if ($remember_me) {
                $extendTTL = 60 * 24 * 7;
                JWTAuth::factory()->setTTL($extendTTL);
            } else {
                JWTAuth::factory()->setTTL($defaultTTL);
            }

            // check if the user is authenticated
            if (!$token = JwtAuth::attempt($userCredentials)){
                return Response::fail("Invalid Credentials", 401);
            }

            $user = JWTAuth::user();
            // Check if email is verified
            if (is_null($user->email_verified_at)) {
                return Response::fail("Email Not Verified", 403);
            }
            // Store the token with user credentials
            UserToken::create(['user_id' => $user->id, 'token' => $token]);

            // set the token expiration time for default and remember me
            $expire_in = $remember_me ? $extendTTL * 60 : $defaultTTL;
            $response = [
                'message' => 'User Logged In Successfully',
                'token' => $token,
                // 'expire_in' => $expire_in,
            ];
            return Response::success($response, 200);
            //exception handling in case of error
        } catch (JWTException $e) {
            return Response::fail("Failed to create Token", 500);
        }
    }

    /*
    |   Refresh the user token if it got expired for the user to keep using the application
    |   if the user is not logged in then the token is invalid.
    */
    public function refresh(Request $request) {
        try {
            $token = JWTAuth::getToken();
            //check if the token is valid
            if(!$token) {
                return Response::fail("Token not Found", 401);
            }
            $newToken = JWTAuth::refresh($token);
            $user = JWTAuth::setToken($newToken)->toUser();
            // delete the old token and create a new token for the user
            UserToken::where('token', $token)->delete();
            UserToken::create(['user_id' => $user->id, 'token' => $newToken]);
            $response = [
                'message' => 'Your Token is refreshed',
                'token' => $newToken,
            ];
            return Response::success($response, 200);

        //exception handling in case of error
        } catch (JWTException $e) {
            return Response::fail("Failed to Create Token, plz try again", 401);
        }
    }

    /*
    |   let the user to logout from the application and it will also delete the token
    */
    public function logout(Request $request) {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::invalidate($token);
            UserToken::where('token', $token)->delete();
            return Response::success("Your logout Successfully", 200);

        //exception handling in case of error
        } catch (JWTException $e) {
            return Response::fail("Failed to logout, plz tyr again", 401);
        }
    }
}
