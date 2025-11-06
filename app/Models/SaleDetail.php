<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleDetail extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sale_id',
        'product_id',
        'product_barcode',
        'product_name',
        'quantity',
        'unit_price',
        'discount_amount',
        'line_total',
    ];

    /**
     * Define los tipos de datos para los atributos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'unit_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    /**
     * Define la relación "pertenece a" con la Venta (Sale).
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Define la relación "pertenece a" con el Producto (Product).
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
