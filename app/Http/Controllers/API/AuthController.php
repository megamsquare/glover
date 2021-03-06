<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\CheckerTrait;

class AuthController extends Controller
{
    use CheckerTrait;

    // Create a new User
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        // Send failed response if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        // Request is valid, create a new user
        $user = User::create([
            'firstName' => $request->get('firstName'),
            'lastName' => $request->get('lastName'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
        ]);

        $this->create_request($user->id, 'Create');

        // Return a successful response
        return response()->json([
            'success' => true,
            'message' => 'You have created an account please wait for admin to approve',
        ], Response::HTTP_CREATED);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Validate Credentials
        $validate = Validator::make($credentials, [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        // Send failed response if validation fails
        if ($validate->fails()) {
            return response()->json($validate->errors()->toJson(), 400);
        }

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Login Credentials',
                ], Response::HTTP_UNAUTHORIZED);
            }
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not Login',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $is_requested = $this->validate_by_user_id(JWTAuth::user()->id);
        if (!$is_requested) {
            return response()->json([
                'success' => false,
                'message' => 'Admin has not approved your account yet',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'success' => true,
            'message' => 'You have been logged in',
            'access_token' => $token
        ], Response::HTTP_OK);
    }

    public function logout(Request $request)
    {
        // Logout user
        try {
            JWTAuth::invalidate($request->bearerToken());
            return response()->json([
                'success' => true,
                'message' => 'You have successfully logged out',
            ]);

        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, the user cannot be logged out',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
