<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orderitem extends Model
{
    protected $fillable = [
        'orders_id',
        'products_id',
        'quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'products_id');
    }
}
