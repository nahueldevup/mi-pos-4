<div>
    @push('styles')
        @livewireStyles
    @endpush

    <h2>📦 Productos</h2>
    <br>

    <div class="row mb-3">
        <div class="col-md-6">
            <input
                wire:model.live="search"
                type="text"
                placeholder="Buscar productos por nombre..."
                class="form-control"
                style="width: 50%;"
            >
            <button class="btn btn-primary" wire:click="create">
                + Nueva Categoría
            </button>
        </div>
        <div class="col-md-6 text-end">
            <button class="add-btn" wire:click="create">
                +
            </button>
        </div>
    </div>

    @if($products->count())
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Precio Venta</th>
                <th>Stock</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($products as $product)
                <tr>
                    <td>{{ $product->barcode }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name ?? 'Sin Categoría' }}</td>
                    <td>${{ number_format($product->sale_price, 2) }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" wire:click="edit({{ $product->id }})">✏️</button>
                        <button class="btn btn-danger btn-sm" wire:click="confirmDelete({{ $product->id }})">🗑️</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $products->links() }}
    @else
        <p>No hay productos registrados.</p>
    @endif


    @if($showModal)
        <div class="modal">
            <div class="modal-content">
                <h2>{{ $currentProduct ? 'Editar Producto' : 'Nuevo Producto' }}</h2>
                <br>

                <form wire:submit.prevent="save">
                    @csrf
                    <label>Código *</label>
                    <input wire:model="barcode" type="text" required>
                    @error('barcode') <span class="error">{{ $message }}</span> @enderror

                    <label>Nombre *</label>
                    <input wire:model="name" type="text" required>
                    @error('name') <span class="error">{{ $message }}</span> @enderror

                    <label>Categoría</label>
                    <select wire:model="category_id">
                        <option value="">-- Sin Categoría --</option>
                        @foreach($categories as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="error">{{ $message }}</span> @enderror

                    <label>Descripción</label>
                    <textarea wire:model="description"></textarea>

                    <label>Precio Compra *</label>
                    <input wire:model="purchase_price" type="number" step="0.01" required>
                    @error('purchase_price') <span class="error">{{ $message }}</span> @enderror

                    <label>Precio Venta *</label>
                    <input wire:model="sale_price" type="number" step="0.01" required>
                    @error('sale_price') <span class="error">{{ $message }}</span> @enderror

                    <label>Stock</label>
                    <input wire:model="stock" type="number" min="0">
                    @error('stock') <span class="error">{{ $message }}</span> @enderror

                    <label>Stock Mínimo</label>
                    <input wire:model="min_stock" type="number" min="0">
                    @error('min_stock') <span class="error">{{ $message }}</span> @enderror

                    <button type="submit" class="btn btn-success">Guardar</button>
                    <button type="button" class="btn btn-danger" wire:click="$set('showModal', false)"> Cerrar</button>
                </form>
            </div>
        </div>
    @endif

    @if($showDeleteModal)
        <div class="modal">
            <div class="modal-content">
                <h3>Confirmar eliminación</h3>
                <p>¿Realmente desea eliminar el producto <strong>{{ $currentProduct->name ?? '' }}</strong>?</p>
                <button type="button" class="btn btn-danger" wire:click="deleteProduct">Sí, ELIMINAR</button>
                <button type="button" class="btn btn-secondary" wire:click="$set('showDeleteModal', false)">CANCELAR</button>
            </div>
        </div>
    @endif

    @push('scripts')
        @livewireScripts
    @endpush
</div>
