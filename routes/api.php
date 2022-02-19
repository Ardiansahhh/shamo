<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use PHPUnit\TextUI\XmlConfiguration\Group;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\GalleriesController;
use App\Http\Controllers\API\ProductCategoryController;
use App\Http\Controllers\API\TransactionController;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('products', [ProductController::class, 'all']);
Route::get('categories', [ProductCategoryController::class, 'all']);
Route::post('addcategory', [ProductCategoryController::class, 'addCategory']);
Route::post('addgallery', [GalleriesController::class, 'addGallery']);
Route::post('addproduct', [ProductController::class, 'addProduct']);
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::post('create', [UserController::class, 'create']);
Route::get('users', [UserController::class, 'all']);
Route::get('findID', [UserController::class, 'find']);
Route::patch('ediID', [UserController::class, 'editID']);
Route::get('tryRegister', [UserController::class, 'tryRegister']);
Route::post('trylogin', [UserController::class, 'trylogin']);
Route::post('try', [UserController::class, 'try']);
Route::get('addProducts', [ProductController::class, 'allProducts']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [UserController::class, 'fetch']);
    Route::post('user', [UserController::class, 'updateProfile']);
    Route::post('logout', [UserController::class, 'logout']);
    Route::post('newPass', [UserController::class, 'newPass']);
    Route::post('transaction', [TransactionController::class, 'all']);
    Route::post('checkout', [TransactionController::class, 'checkout']);
});