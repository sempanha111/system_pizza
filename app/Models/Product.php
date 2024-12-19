<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['categories_id', 'name', 'description', 'price', 'image'];
}
