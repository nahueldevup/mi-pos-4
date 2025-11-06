<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // 1. Importar Auth
use Livewire\Component;

class PosComponent extends Component
{
    // ----- PROPIEDADES PARA LA BÚSQUEDA -----
    public $search = '';

    // ----- PROPIEDADES PARA LA VENTA -----
    public $cart = [];
    public $subtotal = 0;
    public $total = 0;
    public $customer_id;

    // 2. Propiedades nuevas basadas en tu migración
    public $payment_method = 'efectivo'; // Valor por defecto
    public $amount_paid;
    public $change_amount = 0;

    public function render()
    {
        // 1. Obtenemos el término de búsqueda limpio
        $search = trim($this->search);

        $products = []; // Empezar con un array vacío por defecto

        // 2. Solo ejecutamos la búsqueda si el usuario ha escrito algo
        if (!empty($search)) {

            // 3. Empezamos la consulta base (solo productos activos)
            $query = Product::active();

            // 4. Aplicamos tu lógica condicional
            if (is_numeric($search)) {
                // Si es numérico, buscar SÓLO por código de barras
                $query->where('barcode', 'LIKE', '%' . $search . '%');
            }
            else {
                // Si es texto, buscar por nombre O por el nombre de la categoría
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%')
                        ->orWhereHas('category', function($subQuery) use ($search) {
                            $subQuery->where('name', 'LIKE', '%' . $search . '%');
                        });
                });
            }

            // 5. Ejecutamos la consulta final
            $products = $query->limit(10)->get();
        }

        // 6. Cargamos todos los clientes para el <select>
        $customers = Customer::all();

        return view('livewire.pos-component', [
            'products' => $products,
            'customers' => $customers,
        ]);
    }

    public function addProduct(Product $product)
    {
        if (isset($this->cart[$product->id])) {
            $this->cart[$product->id]['quantity']++;
        } else {
            // 3. Añadimos los campos extra que tu migración 'sale_details' necesita
            $this->cart[$product->id] = [
                'name' => $product->name,
                'barcode' => $product->barcode,
                'price' => $product->sale_price,
                'quantity' => 1,
            ];
        }
        $this->calculateTotals();
        $this->search = '';
    }

    public function removeItem($productId)
    {
        unset($this->cart[$productId]);
        $this->calculateTotals();
    }

    public function updated($propertyName)
    {
        if (str_starts_with($propertyName, 'cart.')) {
            $this->calculateTotals();
        }
        // 4. Calculamos el cambio si pagan
        if ($propertyName == 'amount_paid' && !empty($this->amount_paid)) {
            $this->change_amount = $this->amount_paid - $this->total;
        }
    }

    public function saveSale()
    {
        // 5. Validación actualizada
        $this->validate([
            'customer_id' => 'required|exists:customers,id',
            'cart' => 'required|array|min:1',
            'payment_method' => 'required',
            'amount_paid' => 'required|numeric|min:' . $this->total, // Asegurarse de que pague lo suficiente
        ]);

        DB::transaction(function () {
            // 6. Lógica de guardado actualizada para coincidir con tu migración
            $sale = Sale::create([
                'sale_number' => 'VTA-' . date('Ymd') . '-' . uniqid(), //
                'customer_id' => $this->customer_id, //
                'user_id' => 1, // TEMPORAL: Cambiar por Auth::id() cuando haya login //
                'subtotal' => $this->subtotal, //
                'discount_amount' => 0, //
                'tax_amount' => 0, //
                'total' => $this->total, //
                'payment_method' => $this->payment_method, //
                'amount_paid' => $this->amount_paid, //
                'change_amount' => $this->change_amount, //
                'status' => 'completed', //
            ]);

            // 7. Lógica de detalles actualizada para coincidir con tu migración
            foreach ($this->cart as $productId => $item) {
                $lineTotal = $item['price'] * $item['quantity'];

                $sale->details()->create([
                    'sale_id' => $sale->id, // [cite: 10]
                    'product_id' => $productId, // [cite: 10]
                    'product_barcode' => $item['barcode'], // [cite: 10]
                    'product_name' => $item['name'], // [cite: 10]
                    'quantity' => $item['quantity'], // [cite: 10]
                    'unit_price' => $item['price'], // [cite: 11]
                    'discount_amount' => 0, // [cite: 11]
                    'line_total' => $lineTotal, // [cite: 11]
                ]);

                $product = Product::find($productId);
                $product->decrement('stock', $item['quantity']);
            }
        });

        $this->resetState();
        // session()->flash('message', '¡Venta registrada exitosamente!');
    }

    private function calculateTotals()
    {
        $this->subtotal = 0;
        foreach ($this->cart as $item) {
            $this->subtotal += $item['price'] * $item['quantity'];
        }

        // Por ahora, total es igual a subtotal.
        // Aquí podrías sumar impuestos o restar descuentos en el futuro.
        $this->total = $this->subtotal;
        $this->change_amount = 0; // Resetea el cambio
    }

    private function resetState()
    {
        $this->cart = [];
        $this->subtotal = 0;
        $this->total = 0;
        $this->customer_id = null;
        $this->search = '';
        $this->payment_method = 'efectivo';
        $this->amount_paid = null;
        $this->change_amount = 0;
    }
}
