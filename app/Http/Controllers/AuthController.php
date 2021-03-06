<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\AttemptRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Attempt to login with the given credentials.
     *
     * @param AttemptRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function attempt(AttemptRequest $request)
    {
        // get validated attributes
        $credentials = $request->validated();

        // find user by email and check password
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password))
            return response()->json(
                [
                    'message' => __('auth.failed')
                ],
                Response::HTTP_BAD_REQUEST
            );

        // create token
        $token = $user->newToken();

        return response()->json([
            'token' => $token->plainTextToken
        ]);
    }

    /**
     * Get current user.
     *
     * @param Request $request
     * @return \App\Http\Resources\User\UserResource
     */
    public function user(Request $request)
    {
        // get current user
        $user = $request->user();

        return new UserResource($user);
    }

    /**
     * Logout current user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // get current user
        $user = $request->user();

        return response()->json([
            'logged_out' => $user->currentAccessToken()->delete()
        ]);
    }
}
