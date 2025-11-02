<?php
//php artisan make:request StoreProductRequest
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'barcode' => 'required|string|max:255|unique:products',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0', // Sugerencia
            'min_stock' => 'required|integer|min:0', // Sugerencia
            'category_id' => 'nullable|exists:categories,id', // Añadido
            'active' => 'boolean'
        ];
    }
}
