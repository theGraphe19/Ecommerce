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
    Route::post('/logout', 'api\v1\user\AuthController@logoutadmapp');
});

Route::prefix('/admin')->group( function(){
    Route::post('/reg', 'api\v1\admin\AuthController@reg');
    Route::post('/login', 'api\v1\admin\AuthController@login');
    Route::post('/logout', 'api\v1\admin\AuthController@logoutadmapp');
    Route::post('/aboutsave', 'AdminController@aboutussaveadm');
    Route::get('/aboutusapp', 'AdminController@aboutusadmapp');
});