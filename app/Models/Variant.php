<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
	protected $fillable = ['product_id', 'variant_product_id'];
	protected $table = "variants";

	public function products(){
		return $this->belongsTo('App\Models\Product', 'id');
	}
}
