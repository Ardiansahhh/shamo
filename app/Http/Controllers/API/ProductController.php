<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function all(Request $request) {
        $id          = $request->input('id');
        $limit       = $request->input('limit');
        $name        = $request->input('name');
        $description = $request->input('description');
        $tags        = $request->input('tags');
        $categories  = $request->input('categories');
        $price_from  = $request->input('price_from');
        $price_to    = $request->input('price_to');

        //ambil data berdasarkan Id
        if($id) {
            $products = Product::with(['category', 'galleries'])->find($id);
            // jika datanya ada
            if($products) {
                return ResponseFormatter::success(
                    $products, 
                    'Data Successfully'
                );
            } else {
                return ResponseFormatter::error(
                    null, 
                    "Data Empty",
                    404
                );
            }
        }

        $product = Product::with(['category', 'galleries']);
        if($name) {
            $product->where('name', 'like', '%' . $name . '%');
        }

        if($description) {
            $product->where('$description', 'like', '%' . $description . '%');
        }

        if($tags) {
            $product->where('tags', 'like', '%' . $tags . '%');
        }

        if($price_from) {
            $product->where('price', '>=', $price_from);
        }

        if($price_to) {
            $product->where('price', '<=', $price_to);
        }

        if($categories) {
            $product->where('categories', $categories);
        }

        return ResponseFormatter::success(
            $product->paginate($limit),
            'Data Successfully'
        );
    }

    public function addProduct(Request $request) {
        $data = $request->all();
        $product = new Product();
        $product->name          = $request->input('name');
        $product->price         = $request->input('price');
        $product->description   = $request->input('description');
        $product->tags          = $request->input('tags');
        $product->categories_id = $request->input('categories_id');
        $product->save();
        return ResponseFormatter::success(DB::table('products')->get(), 'Data Saved');
    }

}
