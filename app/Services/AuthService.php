<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Mail\ChangeEmailAddressMail;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Mail\SentMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
            auth()->user()->tokens()->delete();

            $result['accessToken'] = $this->createAccessToken()->plainTextToken;
            $result['refreshToken'] = $this->createRefreshToken()->plainTextToken;

            return $this->sendResponse(
                message: 'You are successfully logged in',
                result: $result
            );
        }

        return $this->sendError(
            error: 'Unauthorized',
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
            message: 'You are successfully logged out'
        );
    }

    /**
     * @param $data
     * @return JsonResponse
     */
    public function register($data): JsonResponse
    {
        DB::beginTransaction();

        try {
            $newUser = User::create([
                'first_name' => $data['firstName'],
                'last_name' => $data['lastName'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $newUser->sendEmailVerificationNotification();

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception);

            return $this->sendError(
                error: 'Something went wrong',
                code: 400
            );
        }

        return $this->sendResponse(
            message: 'You have successfully signed up.',
            result: [
                'user' => $this->resource($newUser),
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
                        ->setRememberToken(Str::random(60));

                    $user->save();

                    event(new PasswordReset($user));
                }
            );

        return $this->sendResponse('Password reset successfully');
    }

    /**
     * @param $data
     * @return JsonResponse
     */
    public function verifyEmail($data): JsonResponse
    {
        $user = User::find($data['userId']);

        if (!hash_equals($data['hash'], sha1($user->getEmailForVerification()))) {
            return $this->sendError(
                error: 'Hash is invalid',
                code: 403
            );
        };

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
            return $this->sendResponse('Your email has been successfully verified.');
        }

        return $this->sendResponse('Your email has been successfully verified.');
    }

    public function verifyEmailWithCode($data): JsonResponse
    {
        $user = User::where([
            'id' => $data['userId'],
            'email_verification_code' => $data['code'],
            'email_verification_token' => $data['hash'],
        ])->first();

        if (!$user) {
            return $this->sendError(
                error: 'Invalid credentials',
                code: 422
            );
        }

        // Check if the verification code has expired
        if ($user->email_verification_code_expires_at <= now()) {
            return $this->sendError(
                error: 'Verification code has expired',
                code: 410
            );
        }

        if (!$user->temporary_email) {
            return $this->sendError(
                error: "You haven't set new email address",
                code: 410
            );
        }

        // Update the user's email column only after the new email is verified
        $user->email = $user->temporary_email;
        $user->temporary_email = null;
        $user->email_verification_token = null;
        $user->email_verification_code = null;
        $user->email_verification_code_expires_at = null;
        $user->save();

        return $this->sendResponse('Your email has been successfully changed.');
    }

    /**
     * @param $newEmail
     * @return JsonResponse
     */
    public function changeEmailAddress($data): JsonResponse
    {
        DB::beginTransaction();

        $loggedInUser = request()->user();

        // Generate a new 4 digits verification code
        $verificationCode = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

        // Set the expiration time to 30 minutes from now
        $expiresAt = now()->addMinutes(30);

        // Update the user's email, reset the email verification code, and set the new code and expiration time
        $loggedInUser->update([
            'temporary_email' => $data['email'],
            'email_verification_token' => Str::uuid(),
            'email_verification_code' => $verificationCode,
            'email_verification_code_expires_at' => $expiresAt,
        ]);

        try {
            // Send the email verification email with the new code
            $this->sendEmailVerification($loggedInUser);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception);

            return $this->sendError(
                error: "Couldn't send email. Please check credentials for sending mail or provide valid email address",
                code: 400
            );
        }

        return $this->sendResponse(
            'Email change request sent. Please verify your new email address.'
        );
    }

    /**
     * Refresh access token.
     *
     */
    public function refreshAccessToken(): JsonResponse
    {
        $refreshAccessToken = $this->createAccessToken();

        return $this->sendResponse(
            "Token refreshed successfully",
            ['token' => $refreshAccessToken->plainTextToken]
        );
    }

    /**
     * @return mixed
     */
    private function createAccessToken(): mixed
    {
        return Auth::user()->createToken(
            name: 'access_token',
            expiresAt: now()->addHours(config('sanctum.expiration'))
        );
    }

    /**
     * @return mixed
     */
    private function createRefreshToken(): mixed
    {
        return Auth::user()->createToken(
            name: 'refresh_token',
            expiresAt: now()->addHours(config('sanctum.rt_expiration'))
        );
    }

    protected function sendEmailVerification(User $user): ?SentMessage
    {
        $verificationCode = $user->email_verification_code;

        $verificationUrl = url("/email/verify/{$user->id}/{$user->email_verification_token}?code=$verificationCode");

        return Mail::to($user->temporary_email)
            ->send(new ChangeEmailAddressMail([
                'code' => $verificationCode,
                'url' => $verificationUrl
            ]));
    }
}
