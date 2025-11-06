<?php

namespace App\Livewire;

use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;

class ShowCustomers extends Component
{
    use WithPagination;

    // ----- PROPIEDADES DE BÚSQUEDA Y PAGINACIÓN -----
    public $search = '';

    // ----- PROPIEDADES PARA EL MODAL -----
    public $showModal = false;
    public $showDeleteModal = false;

    // ----- PROPIEDADES DEL MODELO CUSTOMER (PARA EL FORMULARIO) -----
    public ?Customer $currentCustomer = null;
    public $name;
    public $phone;

    /**
     * Define las reglas de validación.
     */
    protected function rules()
    {
        // Como 'name' y 'phone' no son únicos,
        // las reglas de creación y actualización son las mismas.
        return [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20', // Basado en tu migración [cite: 36]
        ];
    }

    /**
     * Muestra la vista con los clientes paginados y filtrados.
     */
    public function render()
    {
        $query = Customer::query();

        if (!empty($this->search)) {
            $query->where('name', 'LIKE', '%' . $this->search . '%')
                ->orWhere('phone', 'LIKE', '%' . $this->search . '%');
        }

        $customers = $query->latest()->paginate(10);

        return view('livewire.show-customers', [
            'customers' => $customers,
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
    public function edit(Customer $customer)
    {
        $this->currentCustomer = $customer;
        $this->name = $customer->name;
        $this->phone = $customer->phone;

        $this->showModal = true;
    }

    /**
     * Guarda el nuevo cliente o actualiza el existente.
     */
    public function save()
    {
        $validatedData = $this->validate();

        if (isset($this->currentCustomer->id)) {
            // Actualizar
            $this->currentCustomer->update($validatedData);
        } else {
            // Crear
            Customer::create($validatedData);
        }

        $this->showModal = false; // Cierra el modal
    }

    /**
     * Muestra el modal de confirmación de borrado.
     */
    public function confirmDelete(Customer $customer)
    {
        $this->currentCustomer = $customer;
        $this->showDeleteModal = true;
    }

    /**
     * Borra el cliente.
     */
    public function deleteCustomer()
    {
        $this->currentCustomer->delete();
        $this->showDeleteModal = false;
    }

    /**
     * Resetea los campos del formulario.
     */
    private function resetInputFields()
    {
        $this->currentCustomer = null;
        $this->name = '';
        $this->phone = '';
    }
}
