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


Auth::routes();

// $this->get('login', 'Auth\LoginController@showLoginForm')->name('login');
// $this->post('login', 'Auth\LoginController@login');
// $this->post('logout', 'Auth\LoginController@logout')->name('logout');



Route::get('/', 'Auth\LoginController@showLoginForm');
//Route::get('/home', 'HomeController@index')->name('home');

Route::middleware('can:accessAdminpanel')->group(function() {
    Route::get('/admin/', 'Adminpanel\Dashboard@index')->name('admin');
    // future adminpanel routes also should belong to the group
});

Route::middleware('can:accessAdminpanel')->group(function() {
    Route::get('/admin/users', 'Adminpanel\UserController@users')->name('manage-users');
    Route::get('/admin/users/new', 'Adminpanel\UserController@newUser');
    Route::get('/admin/users/edit/{user}', 'Adminpanel\UserController@editUser');
    Route::get('/admin/users/destroy/{user}', 'Adminpanel\UserController@deleteUser');
    Route::post('/admin/users/update', 'Adminpanel\UserController@updateUser');
    Route::post('/admin/users/create', 'Adminpanel\UserController@createUser');
});