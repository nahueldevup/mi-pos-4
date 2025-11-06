<div>
    {{-- Para que los estilos de Livewire se carguen --}}
    @push('styles')
        @livewireStyles
    @endpush

    <h2>👥 Clientes</h2>
    <br>

    <div style="display: flex; justify-content: space-between; margin-bottom: 1em;">
        <input
            wire:model.live="search"
            type="text"
            placeholder="Buscar por nombre o teléfono..."
            style="width: 50%;"
        >

        <button class="btn btn-primary" wire:click="create">
            + Nuevo Cliente
        </button>
    </div>

    <table class="table table-striped">
        <thead>
        <tr>
            <th>Nombre</th>
            <th>Teléfono</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($customers as $customer)
            <tr>
                <td>{{ $customer->name }}</td>
                <td>{{ $customer->phone ?? 'N/A' }}</td>
                <td>
                    <button class="btn btn-warning" wire:click="edit({{ $customer->id }})">✏️</button>
                    <button class="btn btn-danger" wire:click="confirmDelete({{ $customer->id }})">🗑️</button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" style="text-align: center;">No se encontraron clientes...</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{ $customers->links() }}

    @if($showModal)
        <div class="modal">
            <div class="modal-content">
                <h2>{{ $currentCustomer ? 'Editar Cliente' : 'Nuevo Cliente' }}</h2>
                <br>

                <form wire:submit.prevent="save">
                    @csrf
                    <label>Nombre *</label>
                    <input wire:model="name" type="text" required>
                    @error('name') <span style="color: red;">{{ $message }}</span> @enderror

                    <label style="display: block; margin-top: 1em;">
                        Teléfono
                    </label>
                    <input wire:model="phone" type="text">
                    @error('phone') <span style="color: red;">{{ $message }}</span> @enderror

                    <hr style="margin-top: 1em;">

                    <button type="submit" class="btn btn-success">💾 Guardar</button>
                    <button type="button" class="btn btn-danger" wire:click="$set('showModal', false)">❌ Cerrar</button>
                </form>
            </div>
        </div>
    @endif

    @if($showDeleteModal)
        <div class="modal">
            <div class="modal-content">
                <h3>Confirmar eliminación</h3>
                <p>¿Realmente desea eliminar al cliente <strong>{{ $currentCustomer->name ?? '' }}</strong>?</p>
                <button type="button" class="btn btn-danger" wire:click="deleteCustomer">Sí, ELIMINAR</button>
                <button type="button" class="btn btn-secondary" wire:click="$set('showDeleteModal', false)">CANCELAR</button>
            </div>
        </div>
    @endif

    {{-- Para que los scripts de Livewire se carguen --}}
    @push('scripts')
        @livewireScripts
    @endpush
</div>
