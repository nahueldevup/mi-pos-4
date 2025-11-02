<?php
//php artisan make:livewire ShowCategories
namespace App\Livewire;

use App\Models\Category;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class ShowCategories extends Component
{
    use WithPagination;
    public $search = '';
    public $showModal = false;
    public $showDeleteModal = false;
    public ?Category $currentCategory = null;
    public $name;
    public $active = true; // Por defecto, una nueva categoría estará activa
    protected function rules()
    {
        if (isset($this->currentCategory->id)) {

            return [// REGLAS DE ACTUALIZACIÓN
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('categories')->ignore($this->currentCategory->id),
                ],
                'active' => 'boolean',
            ];
        } else {

            return [// REGLAS DE CREACIÓN
                'name' => 'required|string|max:255|unique:categories',
                'active' => 'boolean',
            ];
        }
    }

    /**
     * Muestra la vista con las categorías paginadas y filtradas.
     */
    public function render()
    {
        $categories = Category::where('name', 'LIKE', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.show-categories', [
            'categories' => $categories,
        ]);
    }

    /**
     * Abre el modal de creación.
     */
    public function create()
    {
        $this->resetInputFields();
        $this->showModal = true;
    }

    /**
     * Abre el modal de edición y carga los datos.
     */
    public function edit(Category $category)
    {
        $this->currentCategory = $category;
        $this->name = $category->name;
        $this->active = $category->active;

        $this->showModal = true;
    }

    /**
     * Guarda la nueva categoría o actualiza la existente.
     */
    public function save()
    {
        $validatedData = $this->validate();

        if (isset($this->currentCategory->id)) {
            // Actualizar
            $this->currentCategory->update($validatedData);
        } else {
            // Crear
            Category::create($validatedData);
        }

        $this->showModal = false; // Cierra el modal
    }

    /**
     * Muestra el modal de confirmación de borrado.
     */
    public function confirmDelete(Category $category)
    {
        $this->currentCategory = $category;
        $this->showDeleteModal = true;
    }

    /**
     * Borra la categoría.
     */
    public function deleteCategory()
    {
        $this->currentCategory->delete();
        $this->showDeleteModal = false;
    }

    /**
     * Resetea los campos del formulario.
     */
    private function resetInputFields()
    {
        $this->currentCategory = null;
        $this->name = '';
        $this->active = true;
    }
}
