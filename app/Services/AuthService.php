<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthService extends BaseService
{
    /**
     * @param $data
     * @return UserResource
     */
    protected function resource($data): UserResource
    {
        return (new UserResource($data));
    }

    public function attemptLogin($data): JsonResponse
    {
        if (Auth::attempt($data)) {
            $loggedInUser = Auth::user();
            $result['token'] = $loggedInUser
                ->createToken($data['deviceName'] ?? 'UserLoginToken')
                ->plainTextToken;

            return $this->sendResponse(
                message: 'You are successfully logged in',
                result: $result
            );
        }

        return $this->sendError(
            error: 'Unauthorised',
            errorMessages: ['Invalid email or password.'],
            code: 401
        );
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        request()->user()->currentAccessToken()->delete();

        return $this->sendResponse(
            message: 'You are successfully logged in'
        );
    }

    /**
     * @param $data
     * @return JsonResponse
     */
    public function register($data): JsonResponse
    {
        $newUser = User::create([
            'first_name' => $data['firstName'],
            'last_name' => $data['lastName'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        event(new Registered($newUser));

        $accessToken = $newUser
            ->createToken($data['deviceName'] ?? 'UserLoginToken')
            ->plainTextToken;

        return $this->sendResponse(
            message: 'You have successfully signed up.',
            result: [
                'user' => $this->resource($newUser),
                'token' => $accessToken,
            ],
            code: 201
        );
    }

    /**
     * @param $email
     * @return JsonResponse
     * @throws Exception
     */
    public function sendPasswordResetLink($email): JsonResponse
    {
        try {
            $status = Password::broker()
                ->sendResetLink($email);

            return $status === Password::RESET_LINK_SENT
                ? $this->sendResponse('Password reset link sent to your email')
                : $this->sendError(
                    error: 'Unprocessed',
                    errorMessages: ['error' => 'Unable to send password reset link'],
                    code: 400
                );
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(
                error: 'Failed to send email. Please check mail credentials has been set correctly',
                code: 400
            );
        }
    }

    /**
     * @param $data
     * @return JsonResponse
     */
    public function resetPassword($data): JsonResponse
    {
        Password::broker()
            ->reset(
                $data,
                function ($user, string $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                    ])
                        ->setRememberToken(Str::random(60))
                        ->save();

                    event(new PasswordReset($user));
                }
            );

        return $this->sendResponse('Password reset successfully');
    }

}
