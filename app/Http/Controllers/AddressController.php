<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressCreateRequest;
use App\Http\Requests\AddressUpdateRequest;
use App\Http\Resources\AddressCollection;
use App\Http\Resources\AddressResource;
use App\Http\Resources\ErrorResource;
use App\Models\Address;
use App\Models\Contact;
use App\Services\AddressService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{

    public AddressService $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }


    public function create(int $idContact, AddressCreateRequest $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->validated();

        $address = $this->addressService->create($idContact, $user, $data);

        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    public function get(int $idContact, int $idAddress): AddressResource
    {
        $user = Auth::user();

        $address = $this->addressService->get($idContact, $idAddress, $user);

        return new AddressResource($address);
    }

    public function update(int $idContact, int $idAddress, AddressUpdateRequest $request): AddressResource
    {
        $user = Auth::user();
        $data = $request->validated();

        $address = $this->addressService->update($idContact, $idAddress, $user, $data);

        return new AddressResource($address);
    }

    public function delete(int $idContact, int $idAddress): JsonResponse
    {
        $user = Auth::user();

        $boolResponse = $this->addressService->delete($idContact, $idAddress, $user);

        return response()->json([
            'data' => $boolResponse,
        ])->setStatusCode(200);
    }

    public function lists(int $idContact): JsonResponse
    {
        $user = Auth::user();
        $addresses = $this->addressService->list($idContact, $user);
        return (new AddressCollection($addresses))->response()->setStatusCode(200);
    }
}
