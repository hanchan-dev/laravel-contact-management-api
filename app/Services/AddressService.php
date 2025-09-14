<?php

namespace App\Services;

use App\Models\Address;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface AddressService
{
    public function create(int $contactId, Authenticatable|Builder $user, array $data): Address;
    public function get(int $contactId, int $addressId, Authenticatable|Builder $user): Builder|Model;
    public function update(int $contactId, int $addressId, Authenticatable|Builder $user, array $data): Builder|Model;
    public function delete(int $contactId, int $addressId, Authenticatable|Builder $user): bool;
    public function list(int $contactId, Authenticatable|Builder $user): Collection;
}
