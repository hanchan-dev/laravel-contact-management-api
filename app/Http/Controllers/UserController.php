<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

//        if (User::query()->where('username', $data['username'])->exists()) {
//            throw new HttpResponseException(response([
//                "errors" => [
//                    "username" => [
//                        "Username already taken"
//                    ]
//                ]
//            ], 400));
//        }

        if (User::query()->where('username', $data['username'])->exists()) {
            throw new HttpResponseException(
                (new ErrorResource([
                    "username" => [
                        "Username is already taken"
                    ]
                ]))->response()->setStatusCode(400)
            );
        }

        $user = new User($data);
        $user->password = Hash::make($data['password']);
        $user->save();

        return (new UserResource($user))->response()->setStatusCode(201);
    }


    public function login(UserLoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::query()->where('username', $data['username'])->first();
        Log::info($user);

        if(!$user || !Hash::check($data['password'], $user->password)) {
            throw new HttpResponseException(
                (new ErrorResource([
                    'message' => [
                        'username and password combination is incorrect'
                    ]
                ]))->response()->setStatusCode(401)
            );
        }
        $user->token = Str::uuid()->toString();
        $user->save();

        return (new UserResource($user))->response()->setStatusCode(200);
    }

    public function get(Request $request): UserResource
    {
        $user = Auth::user();
        return new UserResource($user);
    }


    public function update(UserUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = Auth::user();

        if (isset($data['name'])){
            $user->name = $data['name'];
        }

        if (isset($data['password'])){
            $user->password = Hash::make($data['password']);
        }

        $user->save();
        return (new UserResource($user))->response()->setStatusCode(200);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = Auth::user();
        $user->token = null;
        $user->save();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }
}
