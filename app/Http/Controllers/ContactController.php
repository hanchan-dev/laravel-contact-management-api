<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactCollection;
use App\Http\Resources\ContactResource;
use App\Http\Resources\ErrorResource;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function create(ContactCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = Auth::user();

        $contact = new Contact($data);
        $contact->user_id = $user->id;
        $contact->save();

        return (new ContactResource($contact))->response()->setStatusCode(201);
    }


    public function get(int $id, Request $request): ContactResource
    {
        $user = Auth::user();
        $contact = Contact::query()->where('id', $id)->where('user_id', $user->id)->first();

        if (!$contact) {
            throw new HttpResponseException(
                (new ErrorResource([
                    'message' => [
                        'Contact not found'
                    ],
                ]))->response()->setStatusCode(404)
            );
        }

        return new ContactResource($contact);
        // mau pake ContactResource mau pake JsonResponse sama aja. pake resource klo default status codenya 200
    }


    public function update(int $id, ContactUpdateRequest $request): ContactResource
    {
        $user = Auth::user();
        $contact = Contact::query()->where('id', $id)->where('user_id', $user->id)->first();

        if (!$contact) {
            throw new HttpResponseException(
                (new ErrorResource([
                    'message' => [
                        'Contact not found'
                    ],
                ]))->response()->setStatusCode(404)
            );
        }

        $data = $request->validated();
        $contact->fill($data);
        $contact->save();

        return new ContactResource($contact);
    }

    public function delete(int $id, Request $request): JsonResponse
    {
        $user = Auth::user();
        $contact = Contact::query()->where('id', $id)->where('user_id', $user->id)->first();

        if (!$contact) {
            throw new HttpResponseException(
                (new ErrorResource([
                    'message' => [
                        'Contact not found'
                    ],
                ]))->response()->setStatusCode(404)
            );
        }

        $contact->delete();
        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }

    public function search(Request $request): JsonResponse
    {
        $user = Auth::user();
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

        return (new ContactCollection($contacts))->response()->setStatusCode(200);
    }
}
