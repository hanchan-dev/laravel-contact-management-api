<?php

namespace App\Services\Implements;

use App\Http\Resources\ErrorResource;
use App\Models\Address;
use App\Services\AddressService;
use App\Services\ContactService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddressServiceImplement implements AddressService
{
    public ContactService $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    public function create(int $contactId, Authenticatable|Builder $user, array $data): Address
    {
        $contact = $this->contactService->get($contactId, $user);
        $address = new Address($data);
        $address->contact_id = $contactId;
        $address->save();

        return $address;
    }

    public function get(int $contactId, int $addressId, Authenticatable|Builder $user): Builder|Model
    {
        $contact = $this->contactService->get($contactId, $user);
        $address = Address::query()->where('contact_id', $contact->id)->where("id", $addressId)->first();

        if (!$address){
            throw new ModelNotFoundException("Address not found");
        }

        return $address;
    }

    public function update(int $contactId, int $addressId, Authenticatable|Builder $user, array $data): Builder|Model
    {
        $address = $this->get($contactId, $addressId, $user);
        $address->fill($data);
        $address->save();

        return $address;
    }

    public function delete(int $contactId, int $addressId, Authenticatable|Builder $user): bool
    {
        $address = $this->get($contactId, $addressId, $user);
        $address->delete();

        return true;
    }

    public function list(int $contactId, Authenticatable|Builder $user): Collection | string
    {
        $contact = $this->contactService->get($contactId, $user);

        $addresses = Address::query()->where('contact_id', $contact->id)->get();
        if (!$addresses){
            throw new ModelNotFoundException("Address not found");
        }
        if ($addresses->isEmpty()){

            return "No Addresses found" ;
        }

        return $addresses;
    }
}
