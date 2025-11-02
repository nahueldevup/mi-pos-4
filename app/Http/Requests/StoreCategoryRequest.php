<?php
//php artisan make:request StoreCategoryRequest
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Permitir que todos creen categorías
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:categories',
            'active' => 'sometimes|boolean', // 'sometimes' la hace opcional
        ];
    }
}
