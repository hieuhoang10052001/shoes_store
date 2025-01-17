<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Manufacture;
use App\Models\ProductCategory;

use Illuminate\Support\Facades\DB;

class CustomerProductController extends Controller
{
    public function index()
    {
        $products = Product::where('product_category_id', 1)->where('status', 1)->where('is_main', 1)->orderBy('price_sale')->paginate(20);
        return view('category_products', ['products' => $products, 'title' => 'HQ Store']);
    }

    public function fetchProduct(Request $request)
    {
        $slug = $request->slug;
        $category = ProductCategory::select('*')->where('slug', '=', $slug)->first();
        $category_id = 0;
        if(isset($category)){
            $category_id = $category['id'];
        }
        $sortby = $request->sortby;
        $min_price = ((int) $request->min_price) / 1000;
        $max_price = ((int) $request->max_price) / 1000;
        if ($min_price && $max_price) {
            if ($sortby == 'price-desc')
                $products = Product::select('id', 'name', 'slug', 'image', 'price_sale', 'price', 'quantity')
                    ->where('status', 1)
                    ->where('is_main', 1)
                    ->where('product_category_id', $category_id)
                    ->where('price_sale', '>=', $min_price)
                    ->where('price_sale', '<=', $max_price)
                    ->orderBy('price_sale', 'desc');
            else if ($sortby == 'name')
                $products = Product::select('id', 'name', 'slug', 'image', 'price_sale', 'price', 'quantity')
                    ->where('status', 1)
                    ->where('is_main', 1)
                    ->where('product_category_id', $category_id)
                    ->where('price_sale', '>=', $min_price)
                    ->where('price_sale', '<=', $max_price)
                    ->orderBy('name');
            else if ($sortby == 'date')
                $products = Product::select('id', 'name', 'slug', 'image', 'price_sale', 'price', 'quantity')
                    ->where('status', 1)
                    ->where('is_main', 1)
                    ->where('product_category_id', $category_id)
                    ->where('price_sale', '>=', $min_price)
                    ->where('price_sale', '<=', $max_price)
                    ->orderBy('created_at', 'desc');
            else
                $products = Product::select('id', 'name', 'slug', 'image', 'price_sale', 'price', 'quantity')
                    ->where('status', 1)
                    ->where('is_main', 1)
                    ->where('product_category_id', $category_id)
                    ->where('price_sale', '>=', $min_price)
                    ->where('price_sale', '<=', $max_price)
                    ->orderBy('price_sale', 'asc');
        } else {
            if ($sortby == 'price-desc')
                $products = Product::select('id', 'name', 'slug', 'image', 'price_sale', 'price', 'quantity')->where('product_category_id', $category_id)->where('is_main', 1)->where('status', 1)->orderBy('price_sale', 'desc');
            else if ($sortby == 'name')
                $products = Product::select('id', 'name', 'slug', 'image', 'price_sale', 'price', 'quantity')->where('product_category_id', $category_id)->where('is_main', 1)->where('status', 1)->orderBy('name');
            else if ($sortby == 'date')
                $products = Product::select('id', 'name', 'slug', 'image', 'price_sale', 'price', 'quantity')->where('product_category_id', $category_id)->where('is_main', 1)->where('status', 1)->orderBy('created_at', 'desc');
            else
                $products = Product::select('id', 'name', 'slug', 'image', 'price_sale', 'price', 'quantity')->where('product_category_id', $category_id)->where('is_main', 1)->where('status', 1)->orderBy('price_sale', 'asc');
        }

        // manufacture
        if ($request->brand) {
            $brands = explode(',', $request->brand);
        }
        $check_manufactures = [];
        if (isset($brands)) {
            $manufactures = [];
            
            for ($i = 0; $i < count($brands); $i++) {
                $manufacture = Manufacture::select('id')->where('slug', $brands[$i])->first();
                if ($manufacture) {
                    array_push($manufactures, $manufacture->id);
                }
            }

            for ($i = 0; $i < count($manufactures); $i++) {
                $check_manufactures[$manufactures[$i]] = true;
            }

            $products = $products->whereIn('manufacturer_id', $manufactures);
        }

        $products = $products->paginate(12);
        $title = $category['name'];
        if ($sortby != null) {
            return view('category_products', compact('title', 'sortby', 'products', 'check_manufactures', 'min_price', 'max_price'));
        }
        return view('category_products', compact('title', 'products', 'check_manufactures', 'min_price', 'max_price'));
    }
}
