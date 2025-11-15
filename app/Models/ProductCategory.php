<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable as AuditableConract;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Support\Facades\Config;

class ProductCategory extends Model implements AuditableConract
{
    use HasFactory , AuditableTrait;


    protected $fillable = [
        'name',
        'description'
    ];

    /**
     * Scope categories considered as fish, using configured aliases.
     */
    public function scopeFish($query)
    {
        $aliases = Config::get('fish.category_aliases', ['Fish', 'Fresh Fish']);
        return $query->whereIn('name', $aliases);
    }
}
