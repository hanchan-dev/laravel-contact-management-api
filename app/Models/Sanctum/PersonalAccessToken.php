<?php

namespace App\Models\Sanctum;

use App\Models\UserSession;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    public function session()
    {
        return $this->hasOne(UserSession::class, 'token_id', 'id');
    }
}
