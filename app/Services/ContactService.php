<?php

namespace App\Services;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface ContactService
{
    public function create(Authenticatable|Builder $user, array $data): Contact;
    public function get(int $contactId, Authenticatable|Builder $user): Builder|Model;
    public function update(int $contactId, Authenticatable|Builder $user, array $data): Builder|Model;
    public function delete(int $contactId, Authenticatable|Builder $user): bool;
    public function search(Authenticatable|Builder $user, Request $request): LengthAwarePaginator;
}
