<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category; // 1. Importar Category
// 2. Importar los Form Requests (debes crearlos)
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    /**
     * Muestra una lista paginada de productos activos.
     */
    public function index()
    {
        // 3. Usamos 'active()' y 'paginate()'
        $products = Product::active()->latest()->paginate(10);

        return view('products.index', compact('products'));
    }

    /**
     * Muestra el formulario de creación con las categorías.
     */
    public function create()
    {
        // 4. Pasamos las categorías al formulario
        $categories = Category::where('active', true)->pluck('name', 'id');

        return view('products.create', compact('categories'));
    }

    /**
     * Guarda un nuevo producto.
     */
    public function store(StoreProductRequest $request)
    {
        // 5. La validación es automática
        Product::create($request->validated());

        return redirect()->route('products.index')
            ->with('success', 'Producto creado exitosamente');
    }

    /**
     * Muestra un producto específico.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Muestra el formulario de edición con el producto y las categorías.
     */
    public function edit(Product $product)
    {
        // 4. Pasamos las categorías (igual que en create)
        $categories = Category::where('active', true)->pluck('name', 'id');

        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Actualiza un producto.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        // 5. La validación es automática
        $product->update($request->validated());

        return redirect()->route('products.index')
            ->with('success', 'Producto actualizado exitosamente');
    }

    /**
     * Elimina un producto.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Producto eliminado exitosamente');
    }
}
