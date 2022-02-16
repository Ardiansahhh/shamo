<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\ProductGallery;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class GalleriesController extends Controller
{
    public function addGallery(request $request) {
        $model = new ProductGallery();
        $model->products_id = $request->input('products_id');
        $model->url = $request->input('url');
        $model->save();
        return ResponseFormatter::success(DB::table('product_galleries')->get(), "Data Saved");
    }
}
