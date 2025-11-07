@extends('layouts.app') {{-- Usa tu layout principal --}}

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md overflow-hidden" id="ticket-content">
            <div class="p-6">
                {{-- Encabezado del Ticket --}}
                <div class="text-center mb-6">
                    <h1 class="text-3xl font-bold">Ticket de Venta</h1>
                    <p class="text-gray-600">Comprobante de Venta N°: {{ $sale->sale_number }}</p>
                    <p class="text-gray-500 text-sm">Fecha: {{ $sale->created_at->format('d/m/Y H:i') }}</p>
                </div>

                {{-- Datos de la Venta --}}
                <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                    <div>
                        <h2 class="font-bold text-gray-800">Vendido por:</h2>
                        <p>{{ $sale->user->name ?? 'Usuario' }}</p>
                    </div>
                    <div>
                        <h2 class="font-bold text-gray-800">Cliente:</h2>
                        <p>{{ $sale->customer->name ?? 'Cliente General' }}</p>
                        @if($sale->customer && $sale->customer->phone)
                            <p>{{ $sale->customer->phone }}</p>
                        @endif
                    </div>
                </div>

                {{-- Tabla de Detalles de Productos --}}
                <h2 class="text-xl font-semibold mb-3 text-gray-800">Detalle de la Compra</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100">
                        <tr>
                            <th class="text-left py-2 px-3">Producto</th>
                            <th class="text-center py-2 px-3">Cant.</th>
                            <th class="text-right py-2 px-3">P. Unit.</th>
                            <th class="text-right py-2 px-3">Subtotal</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($sale->details as $detail)
                            <tr class="border-b">
                                <td class="py-2 px-3">{{ $detail->product_name }}</td>
                                <td class="text-center py-2 px-3">{{ $detail->quantity }}</td>
                                <td class="text-right py-2 px-3">${{ number_format($detail->unit_price, 2) }}</td>
                                <td class="text-right py-2 px-3">${{ number_format($detail->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Totales de la Venta --}}
                <div class="mt-6 flex justify-end">
                    <div class="w-full max-w-xs text-sm">
                        <div class="flex justify-between py-2 border-b">
                            <span class="font-medium text-gray-700">Subtotal:</span>
                            <span class="font-medium">${{ number_format($sale->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <span class="font-medium text-gray-700">Descuento:</span>
                            <span class="font-medium">-${{ number_format($sale->discount_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <span class="font-medium text-gray-700">Impuestos:</span>
                            <span class="font-medium">${{ number_format($sale->tax_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b text-lg font-bold text-black">
                            <span>TOTAL:</span>
                            <span>${{ number_format($sale->total, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-2 mt-2">
                            <span class="font-medium text-gray-700">Método de Pago:</span>
                            <span class="font-medium capitalize">{{ $sale->payment_method }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="font-medium text-gray-700">Pagado:</span>
                            <span class="font-medium">${{ number_format($sale->amount_paid, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="font-medium text-gray-700">Cambio:</span>
                            <span class="font-medium">${{ number_format($sale->change_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Pie de página del ticket --}}
                <div class="text-center mt-8 text-gray-500 text-xs">
                    <p>¡Gracias por su compra!</p>
                </div>
            </div>
        </div>

        {{-- Botones de Acción --}}
        <div class="max-w-2xl mx-auto mt-6 flex justify-between">
            <a href="{{ route('sales.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow transition duration-300">
                <i class="fas fa-arrow-left mr-2"></i> Volver al POS
            </a>
            <button onclick="printTicket()"
                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow transition duration-300">
                <i class="fas fa-print mr-2"></i> Imprimir Ticket
            </button>
        </div>
    </div>

    {{-- Script para imprimir solo el ticket --}}
    <script>
        function printTicket() {
            var printContents = document.getElementById('ticket-content').innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;

            // Recargar la página para restaurar el estado (opcional pero recomendado)
            location.reload();
        }
    </script>
@endsection
