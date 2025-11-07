<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Asumir que el usuario logueado puede vender
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Campos de la Venta (tabla 'sales')
            'customer_id' => 'required|exists:customers,id',
            'payment_method' => ['required', Rule::in(['efectivo', 'tarjeta', 'transferencia'])],
            'total' => 'required|numeric|min:0',
            'amount_paid' => 'required|numeric|min:' . $this->input('total', 0), // Debe ser al menos el total
            'notes' => 'nullable|string',

            // Campos de los Detalles (el 'cart' o carrito)
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0', // Precio del producto en ese momento
            'items.*.product_name' => 'required|string', // Nombre guardado
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'customer_id.required' => 'Debe seleccionar un cliente.',
            'amount_paid.min' => 'El monto pagado no puede ser menor al total.',
            'items.required' => 'El carrito no puede estar vacío.',
            'items.min' => 'Debe agregar al menos un producto.',
        ];
    }
}
