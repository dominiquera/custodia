<?php

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

Route::middleware('can:accessAdminpanel')->group(function() {
    Route::get('/admin/', 'AdminController@index')->name('admin');

    Route::get('/admin/users', 'AdminController@users')->name('manage-users');
    Route::get('/admin/users/new', 'AdminController@newUser');
    Route::get('/admin/users/edit/{user}', 'AdminController@editUser');
    Route::get('/admin/users/destroy/{user}', 'AdminController@deleteUser');
    Route::post('/admin/users/update', 'AdminController@updateUser');
    Route::post('/admin/users/create', 'AdminController@createUser');
});