<?php

namespace App\Http\Requests;

use App\Http\Resources\ErrorResource;
use App\Models\Contact;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ContactUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $idContact = $this->route('id');
        $contact = Contact::query()->find($idContact);

        if (!$contact){
            return false;
        }

        return $this->user()?->can('update', $contact);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'max:100'],
            'last_name' => ['nullable', 'max:100'],
            'email' => ['nullable', 'max:100'],
            'phone' => ['nullable', 'max:20']
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            (new ErrorResource($validator->getMessageBag()))->response()->setStatusCode(400)
        );
    }
}
