<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
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
     * @OA\Get(
     *      path="/email/verify/{id}/{hash}",
     *      tags={"Authentication"},
     *      summary="Verify user email",
     *      description="Verify a user's email address using the provided ID and hash.",
     *      @OA\Parameter(
     *          name="id",
     *          description="User ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="hash",
     *          description="Verification hash",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="string")
     *      ),
     *     @OA\Response(response="200", description="Login successful", @OA\JsonContent()),
     *     @OA\Response(response="422", description="Validation errors", @OA\JsonContent())
     * )
     */
    public function verify($id, $hash): JsonResponse
    {
        return $this->authService->verifyEmail($id, $hash);
    }

    /**
     * @OA\Post(
     *      path="/api/email/verification-resend",
     *      tags={"Authentication"},
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
}
