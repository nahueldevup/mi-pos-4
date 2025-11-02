<?php
//php artisan make:request UpdateCategoryRequest
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Importante para el 'unique'

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Permitir que todos actualicen
    }

    public function rules(): array
    {
        // Obtenemos el ID de la categoría desde la ruta (ej: /categories/5)
        $categoryId = $this->route('category')->id;

        return [
            'name' => ['required', 'string', 'max:255',
                // Asegurarnos de que el nombre sea único,
                // pero ignorando esta misma categoría
                Rule::unique('categories')->ignore($categoryId),
            ],
            'active' => 'sometimes|boolean',
        ];
    }
}
