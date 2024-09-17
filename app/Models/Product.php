<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = [
        'name', 'slug', 'code', 'product_category_id', 'manufacturer_id', 
        'description', 'image', 'price_prime', 'price', 'price_sale', 'maintain',
        'quantity', 'admin_id', 'bought', 'view_count', 'status', 'color_id'
    ];
}