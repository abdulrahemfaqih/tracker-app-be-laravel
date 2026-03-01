<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Service\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{

    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }


    public function register(Request $request): Response
    {
        // validate request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ]);

        // create user
        $user = $this->authService->register($request);

        // create access token
        $token = $user->createToken('auth')->plainTextToken;

        // return response
        return response([
            'message' => __('app.registration_success_verify'),
            'result' => [
                'user' => new UserResource($user),
                'token' => $token
            ]
        ], 201);
    }

    public function login(Request $request): Response
    {
        // validate request data
        $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        // get user
        $user = $this->authService->login($request);

        if (!$user) {
            return response([
                'message' => __('auth.failed'),
            ], 401);
        }

        // create access token
        $token = $user->createToken('auth')->plainTextToken;

        // return response
        return response([
            'message' => $user->email_verified_at ?  __('app.login_success') : __('app.login_success_verify'),
            'result' => [
                'user' => new UserResource($user),
                'token' => $token
            ]
        ], 200);
    }

    public function otp(Request $request): Response
    {

        // get user
        $user = auth()->user();

        // generate otp
        $otp = $this->authService->otp($user);

        // return response
        return response([
            'message' =>   __('app.otp_sent_success')
        ], 200);
    }

    public function verify(Request $request): Response
    {
        // validate the request
        $request->validate([
            'otp' => 'required|numeric|digits:6',
        ]);

        // get user
        $user = auth()->user();

        // verify otp
        $otp = $this->authService->verify($user, $request);

        // return response
        return response([
            'message' =>   __('app.otp_verified_success'),
            'result' => [
                'user' => new UserResource($user),
            ]
        ], 200);
    }

    public function resetOtp(Request $request): Response
    {

        // validate the request
        $request->validate([
            'email' => 'required|string|email|max:255|exists:users,email',
        ]);

        // get user by email
        $user = $this->authService->getUserByEmail($request->email);

        // generate otp
        $otp = $this->authService->otp($user, 'password-reset');

        // return response
        return response([
            'message' =>   __('app.otp_sent_success')
        ], 200);
    }

    public function resetPassword(Request $request): Response
    {
        // validate the request
        $request->validate([
            'email' => 'required|string|email|max:255|exists:users,email',
            'otp' => 'required|numeric|digits:6',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ]);

        // get user by email
        $user = $this->authService->getUserByEmail($request->email);

        // reset password
        $user = $this->authService->resetPassword($user, $request);

        // return response
        return response([
            'message' =>   __('app.password_reset_success')
        ], 200);
    }
}
