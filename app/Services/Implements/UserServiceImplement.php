<?php

namespace App\Services\Implements;

use App\Http\Resources\ErrorResource;
use App\Models\User;
use App\Models\UserSession;
use App\Services\UserService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;

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

    public function login(array $data, string $userAgent, string $ip): array
    {

        $user = User::query()->where('username', $data['username'])->first();
        Log::info($user);

        if(!$user || !Hash::check($data['password'], $user->password)) {
            throw new ModelNotFoundException("username and password combination is incorrect");
        }

        $agent = new Agent();
        $agent->setUserAgent($userAgent);

        // ini coba dulu ambil ip dll, tpi msh bingung simpan dimana

        $platform = $agent->platform();
        $browser = $agent->browser();
        $deviceType = match (true){
            $agent->isDesktop() => 'desktop',
            $agent->isPhone() => 'phone',
            $agent->isTablet() => 'tablet',
            default => 'unknown'
        };
        Log::info("platform: $platform, browser: $browser, deviceType: $deviceType, ip: $ip");

        $token = $user->createToken($deviceType)->plainTextToken;
        $data = [
            "user_id" => $user->id,
            "token_id" => explode('|', $token)[0],
            "platform" => $platform,
            "browser" => $browser,
            "device_type" => $deviceType,
            "ip_address" => $ip,
            "logged_in_at" => now()
        ];

        $userSession = new UserSession($data);
        $userSession->save();

        return [
            'user' => $user,
            'auth_token' => $token
        ];
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

    public function logout(Authenticatable|Builder $user, $tokenPlainText): void
    {
//        $user->tokens()->where('token', hash('sha256', $token))->delete();
        [$id, $token] = explode('|', $tokenPlainText);
        $user->tokens()
            ->where('id', $id)
            ->where('token', hash('sha256', $token))
            ->delete();
    }
}
