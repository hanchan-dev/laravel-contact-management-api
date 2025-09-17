<?php

namespace App\Http\Requests;

use App\Http\Resources\ErrorResource;
use App\Models\Contact;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class ContactCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Contact::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "first_name" => ['required', 'max:100'],
            "last_name" => ['nullable', 'max:100'],
            "email" => ['nullable', 'email', 'max:200'],
            "phone" => ['nullable', 'max:20'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            (new ErrorResource($validator->getMessageBag()))->response()->setStatusCode(400)
        );
    }
}
