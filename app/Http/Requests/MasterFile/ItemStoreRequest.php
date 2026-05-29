<?php

namespace App\Http\Requests\MasterFile;

use Illuminate\Foundation\Http\FormRequest;

class ItemStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:1'],
        ];
    }
}
