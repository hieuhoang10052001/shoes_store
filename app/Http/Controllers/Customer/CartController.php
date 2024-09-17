<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Session;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        $data = [];
        if (Session::has('cart')) {
            $data = Session::get('cart');
        }
        return view('shopping_cart', compact('data'));
    }

    public function addSpecialItem(Request $request)
    {
        $id = $request->id;
        $qty = $request->qty;
        $sizeId = $request->sizeId;
        $sizeName = $request->sizeName;

        $product = Product::select("products.*", "colors.name as colorName")
        ->join("colors", "colors.id", "=", "products.color_id")
        ->where("products.id", "=", $id)
        ->first();

        if (!$product) {
            abort(404);
        }
        $cart = Session::get('cart');

        // if cart is empty then this the first product

        $key = $id . "-" . $sizeId;
        if (!$cart) {
            $cart = [
                $key => [
                    "id" => $product->id,
                    "name" => $product->name,
                    "slug" => $product->slug,
                    "code" => $product->code,
                    "image" => $product->image,
                    "price" => $product->price,
                    "price_sale" => $product->price_sale,
                    "size_id" => $sizeId,
                    "size_name" => $sizeName,
                    "color_name" => $product->colorName,
                    "qty" => $qty,
                ]
            ];
            Session::put('cart', $cart);
            return redirect()->back()->with('success', 'Sản phẩm đã được thêm vào giỏ hàng thành công!');
        }

        // if cart not empty then check if this product exist then increment quantity
        if (isset($cart[$key])) {
            $cart[$key]['qty'] += $qty;
            Session::put('cart', $cart);
            return redirect()->back()->with('success', 'Sản phẩm đã được thêm vào giỏ hàng thành công!');
        }

        // if item not exist in cart then add to cart with quantity = 1
        $cart[$key] = [
            "id" => $product->id,
            "name" => $product->name,
            "slug" => $product->slug,
            "code" => $product->code,
            "image" => $product->image,
            "price" => $product->price,
            "price_sale" => $product->price_sale,
            "size_id" => $sizeId,
            "size_name" => $sizeName,
            "color_name" => $product->colorName,
            "qty" => $qty,
        ];
        Session::put('cart', $cart);
        return redirect()->back()->with('success', 'Sản phẩm đã được thêm vào giỏ hàng thành công!');
    }

    public function addItem(Request $request)
    {
        $id = $request->id;
        $sizeId = $request->sizeId;
        $sizeName = $request->sizeName;
        
        $product = Product::select("products.*", "colors.name as colorName")
        ->join("colors", "colors.id", "=", "products.color_id")
        ->where("products.id", "=", $id)
        ->first();

        if (!$product) {
            abort(404);
        }

        $key = $id . "-" . $sizeId;
        $cart = Session::get('cart');
        // if cart is empty then this the first product
        if (!$cart) {
            $cart = [
                $key => [
                    "id" => $product->id,
                    "name" => $product->name,
                    "slug" => $product->slug,
                    "code" => $product->code,
                    "image" => $product->image,
                    "price" => $product->price,
                    "price_sale" => $product->price_sale,
                    "size_id" => $sizeId,
                    "size_name" => $sizeName,
                    "qty" => 1
                ]
            ];
            Session::put('cart', $cart);
            return redirect()->back()->with('success', 'Sản phẩm đã được thêm vào giỏ hàng thành công!');
        }

        // if cart not empty then check if this product exist then increment quantity
        if (isset($cart[$key])) {
            $cart[$key]['qty']++;
            Session::put('cart', $cart);
        }

        // if item not exist in cart then add to cart with quantity = 1
        $cart[$key] = [
            "id" => $product->id,
            "name" => $product->name,
            "slug" => $product->slug,
            "code" => $product->code,
            "image" => $product->image,
            "price" => $product->price,
            "price_sale" => $product->price_sale,
            "size_id" => $sizeId,
            "size_name" => $sizeName,
            "qty" => 1
        ];
        Session::put('cart', $cart);
    }

    public function remove($key)
    {
        if ($key) {
            $cart = Session::get('cart');
            if (isset($cart[$key])) {
                unset($cart[$key]);
                Session::put('cart', $cart);
            }
        }
    }

    public function clearCart()
    {
        if (Session::has('cart')) {
            Session::forget('cart');
        }
    }

    public function increment(Request $request)
    {
        $key = $request->id;
        if ($key) {
            $cart = Session::get('cart');
            if ($cart[$key]) {
                $cart[$key]['qty']++;
                Session::put('cart', $cart);
            }
        }
    }

    public function decrement(Request $request)
    {
        $key = $request->id;
        if ($key) {
            $cart = Session::get('cart');
            if ($cart[$key]) {
                if ($cart[$key]['qty'] == 1) {
                    unset($cart[$key]);
                    Session::put('cart', $cart);
                } else {
                    $cart[$key]['qty'] -= 1;
                    Session::put('cart', $cart);
                }
            }
        }
    }

    public function getItemNumber()
    {
        if (Session::has('cart')) {
            $cart = Session::get('cart');
            return $cart->totalProduct;
        }
        return 0;
    }
}
