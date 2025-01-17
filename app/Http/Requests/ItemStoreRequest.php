<?php

namespace App\Http\Requests;

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
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'required|in:Food,Drink,Other',
            'initial_stock' => 'required|integer|min:0',
            'image_url' => 'nullable|string|max:255'
        ];
    }
}
