<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeEmailRequest;
use App\Http\Requests\VerifyEmailRequest;
use App\Http\Requests\VerifyNewEmailRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {

    }

    /**
     * @OA\Post (
     *      path="/api/email/verify",
     *      tags={"Email"},
     *      summary="Verify user email",
     *      description="Verify a user's email address using the provided ID and hash.",
     *      @OA\Parameter(
     *         name="userId",
     *         in="query",
     *         description="User's ID",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="hash",
     *         in="query",
     *         description="Verification Hash",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Login successful", @OA\JsonContent()),
     *     @OA\Response(response="422", description="Validation errors", @OA\JsonContent())
     * )
     */
    public function verify(VerifyEmailRequest $verifyEmailRequest): JsonResponse
    {
        return $this->authService->verifyEmail($verifyEmailRequest->validated());
    }

    /**
     * @OA\Post(
     *      path="/api/email/verification-resend",
     *      tags={"Email"},
     *      summary="Resend email verification",
     *      description="Resend the email verification notification to the user.",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(response=200, description="Verification email resent successfully", @OA\JsonContent()),
     *      @OA\Response(response=401, description="Unauthorized - user not authenticated", @OA\JsonContent()),
     *      @OA\Response(response=429, description="Too Many Requests - Throttled", @OA\JsonContent()),
     * )
     */
    public function resendNotification(Request $request): JsonResponse
    {
        $request->user()->sendEmailVerificationNotification();

        return $this->authService->sendResponse('A fresh verification link has been sent to your email address.');
    }

    /**
     * @OA\Post(
     *     path="/api/email/change",
     *     summary="Change user email",
     *     tags={"Email"},
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Verification email resent successfully", @OA\JsonContent()),
     *     @OA\Response(response=400, description="Invalid request", @OA\JsonContent()),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *     @OA\Response(response=422, description="Validation errors", @OA\JsonContent()),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function changeEmail(ChangeEmailRequest $changeEmailRequest): JsonResponse
    {
        return $this->authService->changeEmailAddress($changeEmailRequest->only('email'));
    }

    /**
     * @OA\Post(
     *     path="/api/email/verify-new",
     *     summary="Verify user's new email",
     *     description="Verify the new email address for a user.",
     *     tags={"Email"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="query",
     *         description="User's ID",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="hash",
     *         in="query",
     *         description="Hash",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="code",
     *         in="query",
     *         description="Verification code",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Email changed successfully", @OA\JsonContent()),
     *     @OA\Response(response=400, description="Invalid request", @OA\JsonContent()),
     *     @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *     @OA\Response(response=422, description="Validation errors", @OA\JsonContent()),
     *     @OA\Response(response=410, description="Verification code has expired", @OA\JsonContent()),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function verifyNewEmail(VerifyNewEmailRequest $request): JsonResponse
    {
        return $this->authService->verifyEmailWithCode($request->validated());
    }
}
