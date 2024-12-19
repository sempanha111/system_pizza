<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'users_id',
        'tables_id',
        'status',
        'customerName',
        'contactNumber',
        'paymentMethod',
        'orderType',
        'isDiscount',
        'status_accept',
        'status_cook',
        'status_ready',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'orders_id');
    }


}
