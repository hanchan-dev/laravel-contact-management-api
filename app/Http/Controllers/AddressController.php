<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressCreateRequest;
use App\Http\Requests\AddressUpdateRequest;
use App\Http\Resources\AddressCollection;
use App\Http\Resources\AddressResource;
use App\Http\Resources\ErrorResource;
use App\Models\Address;
use App\Models\Contact;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function create(int $idContact, AddressCreateRequest $request): JsonResponse
    {
        $user = auth()->user();
        $contact  = Contact::query()->where('user_id', $user->id)->where("id", $idContact)->first();

        if (!$contact){
            throw new HttpResponseException(
                (new ErrorResource([
                    'message' => [
                        'Contact not found'
                    ]
                ]))->response()->setStatusCode(404)
            );
        }

        $data = $request->validated();
        $address = new Address($data);
        $address->contact_id = $idContact;
        $address->save();

        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    public function get(int $idContact, int $idAddress): AddressResource
    {
        $user = auth()->user();
        $contact = Contact::query()->where('user_id', $user->id)->where('id', $idContact)->first();
        if (!$contact){
            throw new HttpResponseException(
                (new ErrorResource([
                    'message' => [
                        'Contact not found'
                    ]
                ]))->response()->setStatusCode(404)
            );
        }

        $address = Address::query()->where('contact_id', $contact->id)->where("id", $idAddress)->first();

        if (!$address){
            throw new HttpResponseException(
                (new ErrorResource([
                    'message' => [
                        'Address not found'
                    ]
                ]))->response()->setStatusCode(404)
            );
        }

        return new AddressResource($address);
    }

    public function update(int $idContact, int $idAddress, AddressUpdateRequest $request): AddressResource
    {
        $user = Auth::user();
        $contact = Contact::query()->where('user_id', $user->id)->where('id', $idContact)->first();
        if (!$contact){
            throw new HttpResponseException(
                (new ErrorResource([
                    'message' => [
                        'Contact not found'
                    ]
                ]))->response()->setStatusCode(404)
            );
        }

        $address = Address::query()->where('contact_id', $contact->id)->where("id", $idAddress)->first();

        if (!$address){
            throw new HttpResponseException(
                (new ErrorResource([
                    'message' => [
                        'Address not found'
                    ]
                ]))->response()->setStatusCode(404)
            );
        }

        $data = $request->validated();
        $address->fill($data);
        $address->save();

        return new AddressResource($address);
    }

    public function delete(int $idContact, int $idAddress): JsonResponse
    {
        $user = Auth::user();
        $contact = Contact::query()->where('user_id', $user->id)->where('id', $idContact)->first();
        if (!$contact){
            throw new HttpResponseException(
                (new ErrorResource([
                    'message' => [
                        'Contact not found'
                    ]
                ]))->response()->setStatusCode(404)
            );
        }

        $address = Address::query()->where('contact_id', $contact->id)->where("id", $idAddress)->first();

        if (!$address){
            throw new HttpResponseException(
                (new ErrorResource([
                    'message' => [
                        'Address not found'
                    ]
                ]))->response()->setStatusCode(404)
            );
        }

        $address->delete();
        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }

    public function lists(int $idContact): JsonResponse
    {
        $user = Auth::user();
        $contact = Contact::query()->where('user_id', $user->id)->where('id', $idContact)->first();
        if (!$contact){
            throw new HttpResponseException(
                (new ErrorResource([
                    'message' => [
                        'Contact not found'
                    ]
                ]))->response()->setStatusCode(404)
            );
        }

        $addresses = Address::query()->where('contact_id', $contact->id)->get();
        if (!$addresses){
            throw new HttpResponseException(
                (new ErrorResource([
                    'message' => [
                        'Address not found'
                    ]
                ]))->response()->setStatusCode(404)
            );
        }
        if ($addresses->isEmpty()){
            throw new HttpResponseException(
                response()->json([
                    'data' => [],
                    'description' => [
                        'No addresses'
                    ]
                ])->setStatusCode(200)
            );
        }
        return (new AddressCollection($addresses))->response()->setStatusCode(200);
    }
}
