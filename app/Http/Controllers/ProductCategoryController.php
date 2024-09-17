<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Validator;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::all();
        return view('category.categories_list', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|regex:/^[a-zA-Z0-9_ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽếềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ_(\s)_(\.)_(\,)_(\-)_(\_)]+$/',
            ],

            [
                'name.required' => 'Tên danh mục không được để trống',
                'name.regex' => 'Tên danh mục không được chứa kí tự đặc biệt',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['is' => 'failed', 'error' => $validator->errors()->all()]);
        }
        $data = $request->all();
        unset($data['_token']);
        $data['slug'] = str_slug($data['name']);

        $category = ProductCategory::create($data);

        if (isset($category)) {
            return response()->json(['is' => 'success', 'complete' => 'Danh mục mới đã được thêm']);
        }
        return response()->json(['is' => 'unsuccess', 'uncomplete' => 'Danh mục mới chưa đã được thêm']);
    }

    public function show($id)
    {
        $category = ProductCategory::find($id);
        return $category;
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|regex:/^[a-zA-Z0-9_ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽếềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ_(\s)_(\.)_(\,)_(\-)_(\_)]+$/',
            ],

            [
                'name.required' => 'Tên danh mục không được để trống',
                'name.regex' => 'Tên danh mục không được chứa kí tự đặc biệt',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['is' => 'failed', 'error' => $validator->errors()->all()]);
        }
        $data = $request->all();
        $category = ProductCategory::find($data['id']);
        unset($data['_token']);
        unset($data['id']);
        $data['slug'] = str_slug($data['name']);
        $flag = $category->update($data);
        if ($flag) {
            return response()->json(['is' => 'success', 'complete' => 'Danh mục được cập nhật thành công']);
        }
        return response()->json(['is' => 'unsuccess', 'uncomplete' => 'Danh mục chưa được cập nhật']);
    }

    public function destroy($id)
    {
        $category = ProductCategory::findOrFail($id)->delete();
        if ($category) {
            return response()->json(['is' => 'success', 'complete' => 'Danh mục được xóa thành công']);
        }
        return response()->json(['is' => 'unsuccess', 'uncomplete' => 'Danh mục chưa được xóa']);
    }
}
