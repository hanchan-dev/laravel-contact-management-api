<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactCollection;
use App\Http\Resources\ContactResource;
use App\Http\Resources\ErrorResource;
use App\Models\Contact;
use App\Services\ContactService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public ContactService $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }


    public function create(ContactCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = Auth::user();
        $contact = $this->contactService->create($user, $data);

        return (new ContactResource($contact))->response()->setStatusCode(201);
    }


    public function get(int $id): ContactResource
    {
        $user = Auth::user();
        $contact = $this->contactService->get($id, $user);

        return new ContactResource($contact);
        // mau pake ContactResource mau pake JsonResponse sama aja. pake resource klo default status codenya 200
    }


    public function update(int $id, ContactUpdateRequest $request): ContactResource
    {
        $user = Auth::user();
        $data = $request->validated();
        $contact = $this->contactService->update($id, $user, $data);

        return new ContactResource($contact);
    }

    public function delete(int $id): JsonResponse
    {
        $user = Auth::user();

        $boolResponse = $this->contactService->delete($id, $user);

        return response()->json([
            'data' => $boolResponse
        ])->setStatusCode(200);
    }

    public function search(Request $request): JsonResponse
    {
        $user = Auth::user();

        $contacts = $this->contactService->search($user, $request);
        return (new ContactCollection($contacts))->response()->setStatusCode(200);
    }
}
