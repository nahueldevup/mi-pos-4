<div>
    {{-- Para que los estilos de Livewire se carguen --}}
    @push('styles')
        @livewireStyles
    @endpush

    <h2>🏷️ Categorías</h2>
    <br>

    <div style="display: flex; justify-content: space-between; margin-bottom: 2em;">
        <input
            wire:model.live="search"
            type="text"
            placeholder="Buscar categorías por nombre..."
            style="width: 50%;"
        >

        <button class="btn btn-primary" wire:click="create">
            + Nueva Categoría
        </button>
    </div>

    <table class="table table-striped">
        <thead>
        <tr>
            <th>Nombre</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($categories as $category)
            <tr>
                <td>{{ $category->name }}</td>
                <td>
                    @if($category->active)
                        <span style="color: green;">Activa</span>
                    @else
                        <span style="color: red;">Inactiva</span>
                    @endif
                </td>
                <td>
                    <button class="btn btn-warning" wire:click="edit({{ $category->id }})">✏️</button>
                    <button class="btn btn-danger" wire:click="confirmDelete({{ $category->id }})">🗑️</button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" style="text-align: center;">No se encontraron categorías...</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{ $categories->links() }}

    @if($showModal)
        <div class="modal">
            <div class="modal-content">
                <h2>{{ $currentCategory ? 'Editar Categoría' : 'Nueva Categoría' }}</h2>
                <br>

                <form wire:submit.prevent="save">
                    @csrf
                    <label>Nombre *</label>
                    <input wire:model="name" type="text" required  style="width: 40%;">
                    @error('name') <span style="color: red;">{{ $message }}</span> @enderror

                    <label style="display: block; margin-top: 1em;">
                        <input wire:model="active" type="checkbox">
                        Activa
                    </label>
                    @error('active') <span style="color: red;">{{ $message }}</span> @enderror

                    <hr style="margin-top: 1em;">

                    <button type="submit" class="btn btn-success">Guardar</button>
                    <button type="button" class="btn btn-danger" wire:click="$set('showModal', false)">Cerrar</button>
                </form>
            </div>
        </div>
    @endif

    @if($showDeleteModal)
        <div class="modal">
            <div class="modal-content">
                <h3>Confirmar eliminación</h3>
                <p>¿Realmente desea eliminar la categoría <strong>{{ $currentCategory->name ?? '' }}</strong>?</p>
                <button type="button" class="btn btn-danger" wire:click="deleteCategory">Sí, ELIMINAR</button>
                <button type="button" class="btn btn-secondary" wire:click="$set('showDeleteModal', false)">CANCELAR</button>
            </div>
        </div>
    @endif

    {{-- Para que los scripts de Livewire se carguen --}}
    @push('scripts')
        @livewireScripts
    @endpush
</div>
