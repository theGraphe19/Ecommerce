<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('/user')->group(function() {
    Route::post('/reg', 'api\v1\user\AuthController@reg');
    Route::post('/login', 'api\v1\user\AuthController@login');
    Route::get('/logout', 'api\v1\user\AuthController@logout')->middleware('auth:api');

    Route::get('/products', 'api\v1\user\ProductsController@index');
});

Route::prefix('/admin')->group( function(){
    Route::post('/reg', 'api\v1\admin\AuthController@reg');
    Route::post('/login', 'api\v1\admin\AuthController@login');
    Route::get('/logout', 'api\v1\admin\AuthController@logout')->middleware('auth:admin-api');
    Route::post('/aboutsave', 'AdminController@aboutussaveadm');
    Route::get('/aboutusapp', 'AdminController@aboutusadmapp');

    Route::get('/products', 'api\v1\admin\ProductsController@index');
    Route::post('/products/add', 'api\v1\admin\ProductsController@store');
    Route::get('/products/show/{id}', 'api\v1\admin\ProductsController@show');
    Route::post('/products/update/{id}', 'api\v1\admin\ProductsController@update');
    Route::get('/products/delete/{id}', 'api\v1\admin\ProductsController@destroy');

    Route::post('/products/image/add', 'api\v1\admin\ProductsController@addimage');

    
    Route::get('/category', 'api\v1\admin\CategoriesController@index');
    Route::post('/category/add', 'api\v1\admin\CategoriesController@store');
    Route::get('/category/show/{id}', 'api\v1\admin\CategoriesController@show');
    Route::post('/category/update/{id}', 'api\v1\admin\CategoriesController@update');
    Route::get('/category/delete/{id}', 'api\v1\admin\CategoriesController@destroy');

    Route::get('/subcategory', 'api\v1\admin\SubcategoriesController@index');
    Route::post('/subcategory/add', 'api\v1\admin\SubcategoriesController@store');
    Route::get('/subcategory/show/{id}', 'api\v1\admin\SubcategoriesController@show');
    Route::post('/subcategory/update/{id}', 'api\v1\admin\SubcategoriesController@update');
    Route::get('/subcategory/delete/{id}', 'api\v1\admin\SubcategoriesController@destroy');
}); 