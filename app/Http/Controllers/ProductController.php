<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\Variant;
use Validator;

class ProductController extends Controller
{
    function indexProducts(){
        return Product::select("products.*", "product_categories.name as product_category_name",
            "colors.name as color_name")
            ->join("product_categories", "product_categories.id", "=", "products.product_category_id")
            ->join("colors", "colors.id", "=", "products.color_id")
            ->orderBy('products.id', 'desc')
            ->get();
    }

    public function index()
    {
        $products = ProductController::indexProducts();
        return view('product.products_list', ['products' => $products]);
    }

    public function create()
    {
        $products = Product::select("products.*")
            ->join("product_categories", "product_categories.id", "=", "products.product_category_id")
            ->orderBy('products.id', 'desc')
            ->get();
        return view('product.new_product', ['products' => $products]);
    }

    private function validateRequest($request){
        return Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'code' => 'required',
                'product_category_id' => 'required',
                'manufacturer_id' => 'required',
                'color_id' => 'required',
                'sizes' => 'required',
                'price_prime' => 'required|numeric|min:0',
                'price' => 'required|numeric|min:0',
                'price_sale' => 'required|numeric|min:0',
                'quantity' => 'required|numeric|integer|min:0',
                'status' => 'required',
            ],
            [
                'name.required' => 'Tên sản phẩm không được để trống',
                'code.required' => 'Mã sản phẩm không được để trống',
                'product_category_id.required' => 'Danh mục sản phẩm không được để trống',
                'manufacturer_id.required' => 'Thương hiệu không được để trống',
                'color_id.required' => 'Màu không được để trống',
                'sizes.required' => 'Kích thước không được để trống',
                'price_prime.required' => 'Giá nhập sản phẩm không được để trống',
                'price_prime.numeric' => 'Giá nhập của sản phẩm phải là số',
                'price_prime.min' => 'Giá nhập của sản phẩm phải lớn hơn hoặc bằng 0',
                'price.required' => 'Giá niêm yết sản phẩm không được để trống',
                'price.numeric' => 'Giá niêm yết của sản phẩm phải là số',
                'price.min' => 'Giá niêm yết của sản phẩm phải lớn hơn hoặc bằng 0',
                'price_sale.required' => 'Giá bán đối với sản phẩm không được để trống',
                'price_sale.numeric' => 'Giá giảm đối với sản phẩm phải là số',
                'price_sale.min' => 'Giá giảm đối với sản phẩm phải lớn hơn hoặc bằng 0',
                'quantity.required' => 'Số lượng sản phẩm trong kho không được để trống',
                'quantity.numeric' => 'Số lượng sản phẩm phải là số',
                'quantity.min' => 'Số lượng sản phẩm phải là số lớn hơn hoặc bằng 0',
                'quantity.integer' => 'Số lượng sản phẩm phải là số nguyên',
                'status.required' => 'Trạng thái chỉ định sản phẩm không được để trống',
            ]
        );
    }

    public function store(Request $request)
    {
        $validator = ProductController::validateRequest($request);
        if ($validator->fails()) {
            return response()->json(['is' => 'failed', 'error' => $validator->errors()->all()]);
        }

        $data = $request->all();
        $flag_code = Product::where('code', $data['code'])->first();
        if ($flag_code) {
            return response()->json(['is' => 'unsuccess', 'uncomplete' => 'Mã sản phẩm ' . $data['code'] . ' đã tồn tại']);
        }
        if ($data['price_prime'] > $data['price']) {
            return response()->json(['is' => 'unsuccess', 'uncomplete' => 'Giá nhập phải nhỏ hơn hoặc bằng giá niêm yết của sản phẩm']);
        }
        if ($data['price_prime'] > $data['price_sale']) {
            return response()->json(['is' => 'unsuccess', 'uncomplete' => 'Giá nhập phải nhỏ hơn hoặc bằng giá bán của sản phẩm']);
        }
        if ($data['price'] < $data['price_sale']) {
            return response()->json(['is' => 'unsuccess', 'uncomplete' => 'Giá bán phải nhỏ hơn hoặc bằng giá niêm yết của sản phẩm']);
        }

        unset($data['_token']);
        $time = time();
        $data['slug'] = str_slug($data['name']) . '-' . $time;
        if ($files = $request->file('image')) {
            $destinationPath = 'images/'; // upload path
            $time = time();
            $fileName = $time . "" . date('YmdHis') . "" . $files->hashName();
            $files->move($destinationPath, $fileName);
            $data['image'] = $fileName;
        }
        if (Auth::guard('admin')->check()) {
            $data['admin_id'] = Auth::guard('admin')->user()->id;
        }

        $data['bought'] = 0;
        $data['view_count'] = 0;
        $variant_product_ids = explode(',', $data['variant_product_ids']);

        $sizes_id = explode(',', $data['sizes']);
        unset($data['sizes']);

        unset($data['_token']);
        unset($data['variant_product_ids']);
        $product = Product::create($data);

        if (isset($product)) {
            // variants handling...
            foreach ($variant_product_ids as $variant_product_id) {
                if($variant_product_id != ""){
                    Variant::create([
                        'product_id' => $product->id,
                        'variant_product_id' => $variant_product_id,
                    ]);
                    $product = Product::where('id', $variant_product_id)->update([
                        'is_main' => 0
                    ]);
                }
            };

            // size handling...
            foreach ($sizes_id as $size_id) {
                if($size_id != ""){
                    ProductSize::create([
                        'product_id' => $product->id,
                        'size_id' => $size_id,
                    ]);
                }
            };
            return response()->json(['is' => 'success', 'complete' => 'Sản phẩm của bạn được thêm thành công']);
        }
        return response()->json(['is' => 'unsuccess', 'uncomplete' => 'Sản phẩm của bạn chưa được thêm']);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        $products = Product::select("products.*", "product_categories.name as product_category_name")
        ->join("product_categories", "product_categories.id", "=", "products.product_category_id")
        ->join("colors", "colors.id", "=", "products.color_id")
        ->where("products.id", "!=", $id)
        ->get();
        $variant_products = Variant::where("product_id", "=", $id)->get();
        $variants = [];
        $products_id_str = "";
        foreach ($variant_products as $variant) {
            $variants[$variant->variant_product_id] = true;
            $id_temp = $variant->variant_product_id . ",";
            $products_id_str = $products_id_str . $id_temp;
        }
        return view('product.product_detail', ['product' => $product, 'products' => $products, 'variants' => $variants, 'products_id_str' => $products_id_str]);
    }

    public function update(Request $request)
    {
        $validator = ProductController::validateRequest($request);
        if ($validator->fails()) {
            return response()->json(['is' => 'failed', 'error' => $validator->errors()->all()]);
        }

        $data = $request->all();

        $flag_code = Product::where('code', $data['code'])->where('id', '!=', $data['id'])->first();
        if ($flag_code) {
            return response()->json(['is' => 'unsuccess', 'uncomplete' => 'Mã sản phẩm ' . $data['code'] . ' đã tồn tại']);
        }

        if ($data['price_prime'] > $data['price']) {
            return response()->json(['is' => 'unsuccess', 'uncomplete' => 'Giá nhập phải nhỏ hơn hoặc bằng giá niêm yết của sản phẩm']);
        }
        if ($data['price_prime'] > $data['price_sale']) {
            return response()->json(['is' => 'unsuccess', 'uncomplete' => 'Giá nhập phải nhỏ hơn hoặc bằng giá bán của sản phẩm']);
        }
        if ($data['price'] < $data['price_sale']) {
            return response()->json(['is' => 'unsuccess', 'uncomplete' => 'Giá bán phải nhỏ hơn hoặc bằng giá niêm yết của sản phẩm']);
        }

        unset($data['_token']);
        $time = time();
        $data['slug'] = str_slug($data['name']) . '-' . $time;
        if (Auth::guard('admin')->check()) {
            $data['admin_id'] = Auth::guard('admin')->user()->id;
        }

        $updateData = [
            'name' => $data['name'],
            'code' => $data['code'],
            'product_category_id' => $data['product_category_id'],
            'manufacturer_id' => $data['manufacturer_id'],
            'color_id' => $data['color_id'],
            'description' => $data['description'],
            'maintain' => $data['maintain'],
            'price_prime' => $data['price_prime'],
            'price' => $data['price'],
            'price_sale' => $data['price_sale'],
            'quantity' => $data['quantity'],
            'status' => $data['status'],
        ];

        if ($files = $request->file('image')) {
            $destinationPath = 'images/'; // upload path
            $time = time();
            $fileName = $time . "" . date('YmdHis') . "" . $files->hashName();
            $files->move($destinationPath, $fileName);
            $updateData['image'] = $fileName;
        }

        $product = Product::where('id', $data['id'])->update($updateData);

        if ($product) {
            $variant_product_ids = explode(',', $data['variant_product_ids']);
            if(count($variant_product_ids) !== 0){
                $variant_products_temp = Variant::where("product_id", "=", $data['id'])->get();
                foreach ($variant_products_temp as $item) {
                    $find_record = Variant::where("variant_product_id", "=", $item['variant_product_id'])->where("product_id", "!=", $item['product_id'])->first();
                    if(isset($find_record)){
                        Product::where('id', $item['variant_product_id'])->update([
                            'is_main' => 0
                        ]);
                    }
                    else{
                        Product::where('id', $item['variant_product_id'])->update([
                            'is_main' => 1
                        ]);
                    }
                }
                Variant::where("product_id", "=", $data['id'])->delete();
                foreach ($variant_product_ids as $variant_product_id) {
                    if($variant_product_id != ""){
                        Variant::create([
                            'product_id' => $data['id'],
                            'variant_product_id' => $variant_product_id,
                        ]);
                        Product::where('id', $variant_product_id)->update([
                            'is_main' => 0
                        ]);
                    }
                }
            }

            $sizes_id = explode(',', $data['sizes']);
            if(count($sizes_id) !== 0){
                ProductSize::where("product_id", "=", $data['id'])->delete();
                foreach ($sizes_id as $size_id) {
                    if($size_id != ""){
                        ProductSize::create([
                            'product_id' => $data['id'],
                            'size_id' => $size_id,
                        ]);
                    }
                }
            }

            return response()->json(['is' => 'success', 'complete' => 'Một sản phẩm đã được cập nhật thành công']);
        }
        return response()->json(['is' => 'unsuccess', 'uncomplete' => 'Một sản phẩm chưa được cập nhật thành công']);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id)->delete();
        if ($product) {
            return response()->json(['is' => 'success', 'complete' => 'Một sản phẩm đã được xóa thành công']);
        }
        return response()->json(['is' => 'unsuccess', 'uncomplete' => 'Một sản phẩm chưa được xóa']);
    }

    public function updateStatus(Request $request, $id)
    {
        $data = $request->all();
        unset($data['_token']);
        $product = Product::find($data['id']);

        if ($product->status == 0) {
            $product->status = 1;
        } else {
            $product->status = 0;
        }

        $flag = $product->save();
        if ($flag) {
            return response()->json(['is' => 'success', 'complete' => 'Một sản phẩm đã được cập nhật trạng thái thành công']);
        }
        return response()->json(['is' => 'unsuccess', 'uncomplete' => 'Một sản phẩm chưa được cập nhật trạng thái']);
    }

    public function reportProduct(Request $request){
		if ($request->id == 'max_view'){
			$products = Product::where('status', 1)->orderBy('view_count', 'desc')->take(50)->get();
        	return view('admin.report_product', ['parameter'=> $request->id, 'products' => $products]);
		}
		elseif ($request->id == 'max_bought'){
			$products = Product::where('status', 1)->orderBy('bought', 'desc')->take(50)->get();
        	return view('admin.report_product', ['parameter'=> $request->id, 'products' => $products]);
		}
		elseif ($request->id == 'sold_out'){
			$products = Product::where('status', 1)->where('quantity', 0)->take(50)->get();
        	return view('admin.report_product', ['parameter'=> $request->id, 'products' => $products]);
		}
    }
    
}
