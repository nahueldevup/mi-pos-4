<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

class CategoryController extends Controller
{
    //Muestra una lista de las categorías. (READ)
    public function index()
    {
        //'latest()' las ordena por fecha de creación, de la más nueva a la más vieja
        $categories = Category::latest()->paginate(10);

        return view('categories.index', ['categories' => $categories]);
    }

    //Muestra el formulario para crear una nueva categoría. (CREATE)
    public function create()
    {
        return view('categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        Category::create($request->validated());

        return redirect()->route('categories.index')->with('success', 'Categoría creada.');
    }

    //Muestra una categoría específica.
    public function show(Category $category)
    {
        return view('categories.show', ['category' => $category]);
    }

    //Muestra el formulario para editar una categoría. (UPDATE)
    public function edit(Category $category)
    {
        return view('categories.edit', ['category' => $category]);
    }

    //Actualiza la categoría en la base de datos. (UPDATE)
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return redirect()->route('categories.index')->with('success', 'Categoría actualizada.');
    }

    //Elimina la categoría de la base de datos. (DELETE)
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Categoría eliminada.');
    }
}
