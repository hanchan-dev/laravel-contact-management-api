<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }



    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $this->userService->register($data);

        return (new UserResource($user))->response()->setStatusCode(201);
    }


    public function login(UserLoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        $result = $this->userService->login($data, $request->userAgent(), $request->ip());

        return response()->json([
            'access_token' => $result['auth_token'],
            'token_type' => 'Bearer',
            'user' => new UserResource($result['user'])
        ])->setStatusCode(200);
    }

    public function get(Request $request): UserResource
    {
        $user = Auth::user();
        return new UserResource($user);
    }


    public function update(UserUpdateRequest $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->validated();
        $user = $this->userService->update($user, $data);
        return (new UserResource($user))->response()->setStatusCode(200);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        $token = $request->bearerToken();
        $this->userService->logout($user, $token);
        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }
}
