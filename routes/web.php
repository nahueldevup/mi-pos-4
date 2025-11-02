<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;

Route::get('/', function () {
    return redirect()->route('products.index');
});

Route::get('/categories', function () {
    return redirect()->route('categories.index');
});

// Rutas del CRUD de categorías
Route::resource('categories', CategoryController::class);
// Rutas del CRUD de productos
Route::resource('products', ProductController::class);



// Rutas personalizadas
Route::post('products/{product}/ajustar-stock', [ProductController::class, 'ajustarStock'])
    ->name('products.ajustarStock');

Route::get('buscar-producto', [ProductController::class, 'buscarProducto'])
    ->name('products.buscarproducto');
