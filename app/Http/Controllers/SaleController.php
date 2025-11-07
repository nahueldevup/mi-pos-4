<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Exception;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     * Esto mostrará el historial de ventas.
     */
    public function index()
    {
        // Puedes crear un componente Livewire 'ShowSales' para esta vista
        return view('sales.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Correcto, esto carga la vista con el 'PosComponent'
        return view('sales.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * NOTA: Esta lógica ya la manejas en 'PosComponent@saveSale'.
     * No necesitas implementar este método a menos que quieras
     * crear ventas desde un lugar que NO sea Livewire.
     */
    public function store(Request $request)
    {
        // Lógica de creación (si es necesaria)
    }

    /**
     * Display the specified resource.
     * Esto es para ver el detalle de UNA venta específica.
     */
    public function show(Sale $sale)
    {
        // Cargamos las relaciones para usarlas en la vista
        $sale->load(['customer', 'user', 'details.product']);

        return view('sales.show', compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        // Generalmente las ventas no se editan, se cancelan.
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        // Podrías usar esto para actualizar el 'status'
    }

    /**
     * Remove the specified resource from storage.
     *
     * Usaremos 'destroy' para "Cancelar" la venta y restaurar el stock.
     */
    public function destroy(Sale $sale): RedirectResponse
    {
        // 1. No se puede cancelar una venta ya cancelada
        if ($sale->status == 'cancelled') {
            return Redirect::back()->withErrors(['error' => 'La venta ya está cancelada.']);
        }

        DB::beginTransaction();
        try {
            // 2. Devolver el stock de cada producto
            foreach ($sale->details as $detail) {
                // Solo si el producto sigue existiendo
                if ($detail->product_id) {
                    $product = Product::find($detail->product_id);
                    if ($product) {
                        // Aumentar el stock
                        $product->increment('stock', $detail->quantity);
                    }
                }
            }

            // 3. Marcar la venta como cancelada
            $sale->update(['status' => 'cancelled']);

            DB::commit();

            session()->flash('success', 'Venta cancelada. Se restauró el stock.');
            return Redirect::route('sales.index'); // O a sales.show

        } catch (Exception $e) {
            DB::rollBack();
            // --- LÍNEA CORREGIDA ---
            Log::error("Error al cancelar la venta: " . $e->getMessage());
            // --- FIN DE LA CORRECCIÓN ---
            return Redirect::back()->withErrors(['error' => 'Error al cancelar la venta.']);
        }
    }
}
