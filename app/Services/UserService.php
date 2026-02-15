<?php

namespace App\Services;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

interface UserService
{
    public function register(array $data): User;
    public function login(array $data, string $userAgent, string $ip): array;
    public function update(Authenticatable|Builder $user, array $data): Authenticatable;
    public function logout(Authenticatable|Builder $user, $tokenPlainText): void;
}
