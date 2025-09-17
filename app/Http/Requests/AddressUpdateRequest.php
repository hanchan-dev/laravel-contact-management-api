<?php

namespace App\Http\Requests;

use App\Http\Resources\ErrorResource;
use App\Models\Address;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddressUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $idContact = $this->route('idContact');
        $idAddress = $this->route('idAddress');
        $address = Address::query()
            ->where("id", $idAddress)
            ->where('contact_id', $idContact)
            ->first();

        if (!$address) {
            return false;
        }
        return $this->user()?->can('update', $address);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'street' => ['nullable', 'max:200'],
            'province' => ['nullable', 'max:100'],
            'city' => ['nullable', 'max:100'],
            'country' => ['required', 'max:100'],
            'postal_code' => ['nullable', 'max:10']
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            (new ErrorResource($validator->getMessageBag()))->response()->setStatusCode(400)
        );
    }
}
