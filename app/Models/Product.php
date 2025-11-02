<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'barcode',
        'name',
        'description',
        'purchase_price',
        'sale_price',
        'stock',
        'min_stock',
        'category_id',
        'active'
    ];
    protected $casts = [
        'active' => 'boolean',
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
    ];

    // Esta busca la clave foránea 'category_id'
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    //metodo para obtener la ganancia
    public function ganancia(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->sale_price - $this->purchase_price,
        );
    }
    //metodos para filtrar los productos por activos
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
   //metodo para filtrar los productos por stock bajo
    public function scopeStockBajo($query)
    {
        return $query->whereColumn('stock', '<=', 'min_stock');
    }
}
