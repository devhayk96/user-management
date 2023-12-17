<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

class AuthService
{
    public function attemptLogin($data): JsonResponse
    {
        if (Auth::attempt($data)) {
            $token = Auth::user()
                ->createToken('User Login token')
                ->plainTextToken;

            return Response::json([
                'token' => $token
            ]);
        }

        return Response::json([
            'message' => 'Invalid email or password.'
        ],401);
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        request()->user()->currentAccessToken()->delete();

        return Response::json([
            'message' => "You are logged out."
        ]);
    }

    /**
     * @param $data
     * @return JsonResponse
     */
    public function register($data): JsonResponse
    {
        $user = User::query()->create([
            'first_name' => $data['firstName'],
            'last_name' => $data['lastName'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return Response::json([
            'message' => 'You have successfully signed up.'
        ], 201);
    }
}
