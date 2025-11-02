<?php
//php artisan make:request UpdateProductRequest
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')->id;
        return [
            'barcode' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products')->ignore($productId), // Corregido
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'active' => 'boolean'
        ];
    }
}
