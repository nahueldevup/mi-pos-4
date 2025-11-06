<div>
    {{-- Para que los estilos de Livewire se carguen --}}
    @push('styles')
        @livewireStyles
    @endpush

    <h2>🛒 Punto de Venta</h2>
    <br>

    <div style="display: flex; gap: 2em;">

        <div style="flex: 1;">
            <h3>Buscar Productos</h3>
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Buscar por nombre o código de barras..."
                style="width: 100%;"
            >

            <hr>

            @if(count($products) > 0)
                <table class="table table-striped">
                    <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td>{{ $product->name }} (${{ number_format($product->sale_price, 2) }})</td>
                            <td style="text-align: right;">
                                <button class="btn btn-success" wire:click="addProduct({{ $product->id }})">
                                    +
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @elseif(strlen($search) > 1)
                <p>No se encontraron productos...</p>
            @endif
        </div>

        <div style="flex: 1; border: 1px solid #ccc; padding: 1em; border-radius: 8px;">
            <h3>Detalle de Venta</h3>

            <label>Cliente *</label>
            <select wire:model="customer_id" style="width: 100%;">
                <option value="">-- Seleccione un cliente --</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
            @error('customer_id') <span style="color: red;">{{ $message }}</span> @enderror

            <hr>

            @if(count($cart) > 0)
                <table class="table">
                    <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cant.</th>
                        <th>Precio</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($cart as $productId => $item)
                        <tr>
                            <td>{{ $item['name'] }}</td>
                            <td>
                                <input type="number" wire:model.live="cart.{{ $productId }}.quantity" min="1" style="width: 60px;">
                            </td>
                            <td>${{ number_format($item['price'], 2) }}</td>
                            <td>${{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                            <td>
                                <button class="btn btn-danger" wire:click="removeItem({{ $productId }})">🗑️</button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <hr>

                <div style="text-align: right;">
                    <h3>Subtotal: ${{ number_format($subtotal, 2) }}</h3>
                    <h2>Total: ${{ number_format($total, 2) }}</h2>
                </div>

                <div style="display: flex; gap: 1em; margin-top: 1em;">
                    <div style="flex: 1;">
                        <label>Método de Pago *</label>
                        <select wire:model="payment_method" style="width: 100%;">
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="transferencia">Transferencia</option>
                        </select>
                        @error('payment_method') <span style="color: red;">{{ $message }}</span> @enderror
                    </div>
                    <div style="flex: 1;">
                        <label>Monto Pagado *</label>
                        <input wire:model.live="amount_paid" type="number" step="0.01" style="width: 100%;">
                        @error('amount_paid') <span style="color: red;">{{ $message }}</span> @enderror
                    </div>
                </div>

                @if($change_amount > 0)
                    <h4 style="text-align: right; color: blue; margin-top: 0.5em;">
                        Cambio: ${{ number_format($change_amount, 2) }}
                    </h4>
                @endif

                <br>
                <button
                    class="btn btn-primary"
                    wire:click="saveSale"
                    wire:loading.attr="disabled"
                    style="width: 100%;"
                >
                    <span wire:loading.remove>💾 GUARDAR VENTA</span>
                    <span wire:loading>Guardando...</span>
                </button>
            @else
                <p style="text-align: center;">El carrito está vacío.</p>
            @endif
        </div>

    </div>

    {{-- Para que los scripts de Livewire se carguen --}}
    @push('scripts')
        @livewireScripts
    @endpush
</div>
