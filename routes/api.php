<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TableController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});

Route::controller(AuthController::class)->group(function() {
    Route::post('/adduser', 'adduser')->name('adduser');
    Route::get('/fetchuser', 'fetchuser')->name('fetchuser');
    Route::post('/updateuser/{id}', 'updateuser')->name('updateuser');
    Route::post('/deleteuser/{id}', 'deleteuser')->name('deleteuser');
    Route::post('/login', 'login')->name('login');
    // Route::post('/logout', 'logout')->name('logout');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');


Route::controller(RoleController::class)->group(function(){
    Route::get('/get_role', 'get_role')->name('get_role');
});


Route::controller(CategoriesController::class)->group(function(){
    Route::post('/addcategories', 'addcategories')->name('addcategories');
    Route::get('/get_categories', 'get_categories')->name('get_categories');
    Route::post('/updatecategories/{id}', 'updatecategories')->name('updatecategories');
    Route::post('/deletecategories/{id}', 'deletecategories')->name('deletecategories');
});


Route::controller(ProductController::class)->group(function(){
    Route::post('/addproduct', 'addproduct')->name('addproduct');
    Route::get('/get_product', 'get_product')->name('get_product');
    Route::post('/updateproduct/{id}', 'updateproduct')->name('updateproduct');
    Route::post('/deleteproduct/{id}', 'deleteproduct')->name('deleteproduct');
});


Route::controller(TableController::class)->group(function(){
    Route::post('/addtable', 'addtable')->name('addtable');
    Route::get('/get_table', 'get_table')->name('get_table');
    Route::post('/updatetable/{id}', 'updatetable')->name('updatetable');
    Route::post('/deletetable/{id}', 'deletetable')->name('deletetable');
});


Route::controller(OrderController::class)->group(function(){
    Route::post('/addorder', 'addorder')->name('addorder');
    Route::get('/get_order', 'get_order')->name('get_order');
    Route::post('/changestatusorder/{id}', 'changestatusorder')->name('changestatusorder');
    Route::post('/changeorderfield/{id}', 'changeorderfield')->name('changeorderfield');
    Route::get('/get_order_item/{id}', 'get_order_item')->name('get_order_item');
    Route::get('/get_history', 'get_history')->name('get_history');
});
