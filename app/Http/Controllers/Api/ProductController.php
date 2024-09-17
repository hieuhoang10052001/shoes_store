<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSize;

class ProductController extends Controller
{
    private $status = 200;
    
    public function getAllProduct(){
        $products = Product::select('name', 'slug', 'image')->where('status', 1)->orderBy('created_at')->get();
        return response()->json($products, $this->status, [], JSON_UNESCAPED_UNICODE);
    }

    public function sizesOfProduct($id){
        $sizes = ProductSize::select(
                "products.id", 
                "sizes.id as sizeId", 
                "sizes.name as sizeName", 
            )
            ->join('products', 'products.id', '=', 'product_sizes.product_id')
            ->join("sizes", "sizes.id", "=", "product_sizes.size_id")
            ->where("product_sizes.product_id", "=", $id)
            ->get();

        return response()->json($sizes, $this->status, [], JSON_UNESCAPED_UNICODE);
    }
}
