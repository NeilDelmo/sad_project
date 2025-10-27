<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasUuids, HasFactory;
    protected $primaryKey = 'product_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'supplier_id',
        'category_id',
        'name',
        'description',
        'freshness_metric',
        'unit_price',
        'available_quantity',
        'seasonality_factor',
        'quality_rating',
    ];

    public function supplier(): BelongsTo{
        return $this->belongsTo(User::class, 'supplier_id', 'id');
    }

    public function category(): BelongsTo{
        return $this->belongsTo(ProductCategory::class, 'category_id', 'category_id');
    }
}
