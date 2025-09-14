<?php

namespace App\Services\Implements;

use App\Http\Resources\ErrorResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserServiceImplement implements UserService
{

    public function register(array $data): User
    {

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

        return $user;
    }

    public function login(array $data): Builder|Model
    {
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

        return $user;
    }

    public function update(Authenticatable|Builder $user, array $data): Authenticatable
    {
        if (isset($data['name'])){
            $user->name = $data['name'];
        }

        if (isset($data['password'])){
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return $user;
    }

    public function logout(Authenticatable|Builder $user): Authenticatable
    {
        $user->token = null;
        $user->save();

        return $user;
    }
}
