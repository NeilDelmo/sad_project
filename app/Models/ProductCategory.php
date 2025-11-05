<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable as AuditableConract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class ProductCategory extends Model implements AuditableConract
{
    use HasFactory , AuditableTrait;


    protected $fillable = [
        'name',
        'description'
    ];
}
