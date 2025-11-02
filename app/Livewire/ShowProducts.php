<?php
//php artisan make:livewire ShowProducts
namespace App\Livewire;
use Illuminate\Validation\Rule;
use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ShowProducts extends Component
{
    use WithPagination;

    // ----- PROPIEDADES DE BÚSQUEDA Y PAGINACIÓN -----
    public $search = '';

    // ----- PROPIEDADES PARA EL MODAL -----
    public $showModal = false;
    public $showDeleteModal = false;

    // ----- PROPIEDADES DEL MODELO PRODUCT (PARA EL FORMULARIO) -----
    public ?Product $currentProduct= null; // El producto que estamos editando/borrando
    public $barcode;
    public $name;
    public $description;
    public $purchase_price;
    public $sale_price;
    public $stock;
    public $min_stock;
    public $category_id;
    public $active = true;

    protected function rules()
    {
        // Comprueba si estamos actualizando (si $currentProduct existe)
        if (isset($this->currentProduct->id)) {

            // REGLAS DE ACTUALIZACIÓN
            $productId = $this->currentProduct->id;
            return [
                'barcode' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('products')->ignore($productId),
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

        } else {

            // REGLAS DE CREACIÓN
            return [
                'barcode' => 'required|string|max:255|unique:products',
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
    // ----- MÉTODO PRINCIPAL DE RENDERIZADO -----
    public function render()
    {
        // 1. Obtenemos el término de búsqueda limpio
        $search = trim($this->search);
        // 2. Empezamos la consulta base (solo productos activos)
        $query = Product::active();
        // 3. Aplicamos tu lógica condicional
        if (empty($search)) {
            // Si no hay búsqueda, no se añade ningún filtro extra
        }
        elseif (is_numeric($search)) {
            // Si es numérico, buscar SÓLO por código de barras
            $query->where('barcode', 'LIKE', '%' . $search . '%');
        }
        else {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('category', function($subQuery) use ($search) {
                        $subQuery->where('name', 'LIKE', '%' . $search . '%');
                    });
            });
        }
        // 4. Terminamos la consulta con orden y paginación
        $products = $query->latest()->paginate(10);
        // Pasamos las categorías al componente para el <select>
        $categories = Category::where('active', true)->pluck('name', 'id');

        return view('livewire.show-products', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    // ----- MÉTODO PARA ABRIR MODAL DE CREACIÓN -----
    public function create()
    {
        $this->resetInputFields();
        $this->showModal = true;
    }

    // ----- MÉTODO PARA ABRIR MODAL DE EDICIÓN -----
    public function edit(Product $product)
    {
        $this->currentProduct = $product;
        $this->barcode = $product->barcode;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->purchase_price = $product->purchase_price;
        $this->sale_price = $product->sale_price;
        $this->stock = $product->stock;
        $this->min_stock = $product->min_stock;
        $this->category_id = $product->category_id;
        $this->active = $product->active;

        $this->showModal = true;
    }

    // ----- MÉTODO PARA GUARDAR (CREAR O ACTUALIZAR) -----
    public function save()
    {
        // 1. Livewire buscará automáticamente el método rules()
        //    y validará las propiedades públicas del componente.
        $validatedData = $this->validate();

        if (isset($this->currentProduct->id)) {
            // Actualizar
            $this->currentProduct->update($validatedData);
            // session()->flash('message', 'Producto actualizado.');
        } else {
            // Crear
            Product::create($validatedData);
            // session()->flash('message', 'Producto creado.');
        }

        $this->showModal = false;
    }

    // ----- MÉTODO PARA ABRIR MODAL DE BORRADO -----
    public function confirmDelete(Product $product)
    {
        $this->currentProduct = $product;
        $this->showDeleteModal = true;
    }

    // ----- MÉTODO PARA BORRAR -----
    public function deleteProduct()
    {
        $this->currentProduct->delete();
        $this->showDeleteModal = false;
        // session()->flash('message', 'Producto eliminado.');
    }

    // ----- UTILIDAD PARA LIMPIAR CAMPOS -----
    private function resetInputFields()
    {
        $this->currentProduct = null;
        $this->barcode = '';
        $this->name = '';
        $this->description = '';
        $this->purchase_price = '';
        $this->sale_price = '';
        $this->stock = 0;
        $this->min_stock = 5;
        $this->category_id = null;
        $this->active = true;
    }
}
