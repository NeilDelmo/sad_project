<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable as AuditableConract;
use OwenIt\Auditing\Auditable as AuditableTrait;
use App\Models\User;
use App\Models\ProductCategory;

class Product extends Model implements AuditableConract
{
    use HasFactory , AuditableTrait;

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
        return $this->belongsTo(ProductCategory::class, 'category_id', 'id');
    }
}
