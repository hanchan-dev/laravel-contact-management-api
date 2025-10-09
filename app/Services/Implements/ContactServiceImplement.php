<?php

namespace App\Services\Implements;

use App\Http\Resources\ErrorResource;
use App\Models\Contact;
use App\Models\User;
use App\Services\ContactService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ContactServiceImplement implements ContactService
{

    public function create(Authenticatable|Builder $user, array $data): Contact
    {
        $contact = new Contact($data);
        $contact->user_id = $user->id;
        $contact->save();

        return $contact;
    }

    public function get(int $contactId, Authenticatable|Builder $user): Builder|Model
    {
        $contact = Contact::query()->where('id', $contactId)->where('user_id', $user->id)->first();

        if (!$contact) {
            throw new ModelNotFoundException("Contact not found");
        }

        return $contact;
    }

    public function update(int $contactId, Authenticatable|Builder $user, array $data): Builder|Model
    {
        $contact = $this->get($contactId, $user);

        $contact->fill($data);
        $contact->save();

        return $contact;
    }

    public function delete(int $contactId, Authenticatable|Builder $user): bool
    {
        $contact = $this->get($contactId, $user);
        $contact->delete();

        return true;
    }

    public function search(Authenticatable|Builder $user, Request $request): LengthAwarePaginator
    {
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);

        $contacts = Contact::query()->where('user_id', $user->id);
        $contacts->where(function (Builder $builder) use ($request) {
            $name = $request->input('name');
            if ($name){
                $builder->orWhere('first_name', 'like', '%' . $name . '%');
                $builder->orWhere('last_name', 'like', '%' . $name . '%');
            }

            $email = $request->input('email');
            if ($email){
                $builder->orWhere('email', 'like', '%' . $email . '%');
            }

            $phone = $request->input('phone');
            if ($phone){
                $builder->orWhere('phone', 'like', '%' . $phone . '%');
            }
        });

        $contacts = $contacts->paginate(perPage: $size, page: $page);

        return $contacts;
    }
}
