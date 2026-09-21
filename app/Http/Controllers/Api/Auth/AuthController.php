<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\RequestPhoneVerificationRequest;
use App\Http\Requests\Api\Auth\UpdateProfileRequest;
use App\Http\Requests\Api\Auth\VerifyPhoneRequest;
use App\Models\User;
use App\Notifications\PhoneVerificationCodeNotification;
use App\Services\NetworkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    private const VALID_PROVIDERS = ['google', 'facebook', 'github'];

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::query()->create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
        ]);

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'data' => [
                'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
                'token' => $token,
            ],
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = $request->authenticate();

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'data' => [
                'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
                'token' => $token,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->tokens()->delete();

        auth()->forgetGuards();

        return response()->json(status: 204);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'data' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'role' => $user->role],
        ]);
    }

    #[OA\Put(
        path: '/api/user',
        summary: 'Update user profile',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    required: ['name', 'email'],
                    properties: [
                        new OA\Property(property: 'name', type: 'string'),
                        new OA\Property(property: 'email', type: 'string', format: 'email'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Profile updated successfully'
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error'
            ),
        ]
    )]
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $request->user()->update($request->validated());

        return response()->json([
            'data' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'role' => $request->user()->role,
            ],
        ]);
    }

    public function requestPhoneVerification(RequestPhoneVerificationRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->update(['phone' => $request->validated('phone')]);

        $code = $user->generatePhoneVerificationToken();
        $user->notify(new PhoneVerificationCodeNotification($code));

        return response()->json([
            'message' => 'Verification code sent to your phone',
            'phone' => $user->phone,
        ]);
    }

    public function verifyPhone(VerifyPhoneRequest $request): JsonResponse
    {
        $user = $request->user();
        $verified = $user->verifyPhone($request->validated('code'));

        if (!$verified) {
            return response()->json(['message' => 'Invalid verification code'], 422);
        }

        return response()->json([
            'message' => 'Phone verified successfully',
            'phone_verified_at' => $user->phone_verified_at,
        ]);
    }

    public function socialiteRedirect(string $provider): JsonResponse
    {
        if (! in_array($provider, self::VALID_PROVIDERS, true)) {
            return response()->json(['message' => 'Invalid provider'], 400);
        }

        try {
            $url = Socialite::driver($provider)
                ->stateless()
                ->redirect()
                ->getTargetUrl();

            return response()->json(['url' => $url]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to redirect'], 400);
        }
    }

    public function socialiteCallback(string $provider, NetworkService $networkService): JsonResponse
    {
        if (! in_array($provider, self::VALID_PROVIDERS, true)) {
            return response()->json(['message' => 'Invalid provider'], 400);
        }

        try {
            $providerUser = Socialite::driver($provider)
                ->stateless()
                ->user();

            $result = $networkService->handleCallback($provider, $providerUser);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to authenticate with provider'], 400);
        }

        return response()->json([
            'data' => [
                'user' => [
                    'id' => $result['user']->id,
                    'name' => $result['user']->name,
                    'email' => $result['user']->email,
                    'status' => $result['user']->status,
                    'is_new' => $result['is_new'],
                ],
                'token' => $result['token'],
            ],
        ]);
    }
    public function unlinkNetwork(string $provider, Request $request, NetworkService $networkService): JsonResponse
    {
        if (!in_array($provider, self::VALID_PROVIDERS)) {
            return response()->json(['message' => 'Invalid provider'], 400);
        }

        $user = $request->user();

        if (!$networkService->hasNetwork($user, $provider)) {
            return response()->json(['message' => 'Network not linked'], 404);
        }

        $networkService->unlinkNetwork($user, $provider);

        return response()->json(['message' => 'Network unlinked successfully']);
    }
}
