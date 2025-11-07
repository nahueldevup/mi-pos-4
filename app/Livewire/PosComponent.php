<?php
//php artisan make:livewire PosComponent
namespace App\Livewire;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // Importar Auth
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

    // Propiedades nuevas basadas en tu migración
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
        // --- CAMBIO: Verificar stock antes de agregar ---
        // Obtener la cantidad actual en el carrito (si existe)
        $quantityInCart = $this->cart[$product->id]['quantity'] ?? 0;

        if ($product->stock <= $quantityInCart) {
            session()->flash('stock_error', 'No hay más stock para: ' . $product->name);
            return;
        }
        // --- FIN CAMBIO ---

        if (isset($this->cart[$product->id])) {
            $this->cart[$product->id]['quantity']++;
        } else {
            // Añadimos los campos extra que tu migración 'sale_details' necesita
            $this->cart[$product->id] = [
                'name' => $product->name,
                'barcode' => $product->barcode,
                'price' => $product->sale_price,
                'quantity' => 1,
                'stock' => $product->stock // --- CAMBIO: Guardar stock para validar ---
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
        // --- CAMBIO: Validar stock al cambiar cantidad manualmente ---
        if (str_starts_with($propertyName, 'cart.')) {
            // Extraer el ID del producto y la propiedad (ej. 'cart.1.quantity')
            $parts = explode('.', $propertyName);
            $productId = $parts[1];
            $property = $parts[2];

            if ($property === 'quantity') {
                $item = $this->cart[$productId];
                if ($item['quantity'] > $item['stock']) {
                    $this->cart[$productId]['quantity'] = $item['stock'];
                    session()->flash('stock_error', 'Stock máximo alcanzado para: ' . $item['name']);
                }
                if ($item['quantity'] < 1) {
                    $this->cart[$productId]['quantity'] = 1;
                }
            }
            // --- FIN CAMBIO ---

            $this->calculateTotals();
        }

        // Calculamos el cambio si pagan
        if ($propertyName == 'amount_paid' && !empty($this->amount_paid)) {
            $this->change_amount = $this->amount_paid - $this->total;
        }
    }

    public function saveSale()
    {
        // Validación actualizada
        $this->validate([
            'customer_id' => 'required|exists:customers,id',
            'cart' => 'required|array|min:1',
            'payment_method' => 'required',
            'amount_paid' => 'required|numeric|min:' . $this->total, // Asegurarse de que pague lo suficiente
        ]);

        // --- CAMBIO: Definir $sale fuera de la transacción ---
        $sale = null;

        DB::transaction(function () use (&$sale) { // --- CAMBIO: Pasar $sale por referencia

            // Lógica de guardado actualizada para coincidir con tu migración
            $sale = Sale::create([ // --- CAMBIO: Asignar a la variable $sale externa
                'sale_number' => 'VTA-' . date('Ymd') . '-' . uniqid(),
                'customer_id' => $this->customer_id,
                'user_id' => Auth::id() ?? 1, // --- CAMBIO: Usar Auth::id() si existe
                'subtotal' => $this->subtotal,
                'discount_amount' => 0,
                'tax_amount' => 0,
                'total' => $this->total,
                'payment_method' => $this->payment_method,
                'amount_paid' => $this->amount_paid,
                'change_amount' => $this->change_amount,
                'status' => 'completed',
            ]);

            // Lógica de detalles actualizada para coincidir con tu migración
            foreach ($this->cart as $productId => $item) {
                // --- CAMBIO: Validar stock OTRA VEZ por si acaso (doble seguridad) ---
                $product = Product::find($productId);
                if ($product->stock < $item['quantity']) {
                    // Si no hay stock, revertimos todo
                    throw new \Exception('No hay suficiente stock para el producto: ' . $product->name);
                }
                // --- FIN CAMBIO ---

                $lineTotal = $item['price'] * $item['quantity'];

                $sale->details()->create([
                    'product_id' => $productId,
                    'product_barcode' => $item['barcode'],
                    'product_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'discount_amount' => 0,
                    'line_total' => $lineTotal,
                ]);

                $product->decrement('stock', $item['quantity']);
            }
        }); // --- FIN DE LA TRANSACCIÓN ---

        // --- CAMBIO: Si la transacción fue exitosa, $sale existirá ---
        if ($sale) {
            $this->resetState();

            // --- ¡AQUÍ ESTÁ LA REDIRECCIÓN AL TICKET! ---
            return redirect()->route('sales.show', $sale);

        } else {
            // Opcional: Manejar un error inesperado si la venta no se creó
            session()->flash('error', 'Ocurrió un error inesperado al guardar la venta.');
        }
    }

    private function calculateTotals()
    {
        $this->subtotal = 0;
        foreach ($this->cart as $item) {
            $this->subtotal += $item['price'] * ($item['quantity'] > 0 ? $item['quantity'] : 1);
        }

        // Por ahora, total es igual a subtotal.
        $this->total = $this->subtotal;
        $this->change_amount = 0; // Resetea el cambio
        $this->amount_paid = ''; // Resetea el pago
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
