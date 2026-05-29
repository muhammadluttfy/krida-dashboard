<?php

namespace App\Http\Requests\MasterFile;

use Illuminate\Foundation\Http\FormRequest;

class CustomerStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone_number' => ['nullable', 'string', 'max:15', 'regex:/^08[0-9]+$/'],
        ];
    }
}
