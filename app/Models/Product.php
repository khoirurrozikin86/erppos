<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'barcode',
        'name',
        'category_id',
        'unit_id',
        'product_type',
        'purchase_price',
        'sales_price',
        'min_stock',
        'max_stock',
        'taxable',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'sales_price' => 'decimal:2',
            'min_stock' => 'decimal:4',
            'max_stock' => 'decimal:4',
            'taxable' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
