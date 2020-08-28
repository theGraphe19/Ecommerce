<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::post('/users/logout', 'Auth\LoginController@userLogout')->name('user.logout');

Route::prefix('/admin')->group( function(){
    Route::get('/login', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');
    Route::post('/login', 'Auth\AdminLoginController@login')->name('admin.login.submit');
    Route::get('', 'AdminController@index')->name('admin.dashboard')->middleware('auth:admin');
    Route::post('/logout', 'Auth\AdminLoginController@adminLogout')->name('admin.logout');
    Route::get('/register', 'Auth\AdminRegisterController@showRegisterForm')->name('admin.register');
    Route::post('/register', 'Auth\AdminRegisterController@register')->name('admin.register.submit');
});

Route::prefix('/app')->group( function(){
    Route::post('/reg', 'AdminController@regadmapp');
    Route::post('/login', 'AdminController@loginadmapp');
    Route::post('/aboutsave', 'AdminController@aboutussaveadm');
    Route::get('/aboutusapp', 'AdminController@aboutusadmapp');
});