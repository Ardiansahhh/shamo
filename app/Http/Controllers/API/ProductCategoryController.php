<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ProductCategoryController extends Controller
{
    public function all(Request $request)
    {
        $id    = $request->input('id');
        $limit = $request->input('limit');
        $name  = $request->input('name');
        $show  = $request->input('show_products');

        //ambil data berdasarkan Id
        if ($id) {
            $categories = ProductCategory::with(['products'])->find($id);
            // jika datanya ada
            if ($categories) {
                return ResponseFormatter::success(
                    $categories,
                    'Data Kategori berhasil diambil'
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    "Data Empty",
                    404
                );
            }
        }
        $category = ProductCategory::query();
        if ($category) {
            $category->where('name', 'like', '%' . $name . '%');
        }

        if ($show) {
            $category->with(['products']);
        }

        return ResponseFormatter::success(
            $category->paginate($limit),
            'Data Successfully'
        );
    }

    public function addCategory(Request $request)
    {
        $name = $request->name;
        if (empty($name)) {
            return ResponseFormatter::error(null, 'Name tidak boleh kosong');
        } else {
            $model = new ProductCategory();
            $category = DB::table('product_categories')->where('name', 'like', '%' . $name . '%')->first();
            if ($category) {
                return ResponseFormatter::error(null, 'Kategori sudah ada');
            } else {
                $model->name = $name;
                $model->save();
                return ResponseFormatter::success(DB::table('product_categories')->get(), 'Data Saved');
            }
        }
    }
}
