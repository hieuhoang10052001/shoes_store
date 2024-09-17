<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
	protected $fillable = ['product_id', 'size_id'];
	protected $table = "product_sizes";

	public function products(){
		return $this->belongsTo('App\Models\Product', 'id');
	}
}
