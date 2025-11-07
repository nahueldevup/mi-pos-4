<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
//php artisan make:request UpdateCustomerRequest
class UpdateCustomerRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // No necesitamos 'unique' para el teléfono, así que es igual al StoreRequest
        return [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ];
    }
}
