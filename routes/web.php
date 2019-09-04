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
    Route::get('/admin/', 'Admin\AdminController@index')->name('admin');

    Route::get('/admin/users', 'Admin\UserController@users')->name('manage-users');
    Route::get('/admin/users/new', 'Admin\UserController@newUser');
    Route::get('/admin/users/edit/{user}', 'Admin\UserController@editUser');
    Route::get('/admin/users/destroy/{user}', 'Admin\UserController@deleteUser');
    Route::post('/admin/users/update', 'Admin\UserController@updateUser');
    Route::post('/admin/users/create', 'Admin\UserController@createUser');

    Route::get('/admin/maintenance_items', 'Admin\MaintenanceItemController@maintenanceItems')->name('manage-items');
    Route::get('/admin/maintenance_items/new', 'Admin\MaintenanceItemController@newMaintenanceItem');
    Route::get('/admin/maintenance_items/edit/{maintenance_item_id}', 'Admin\MaintenanceItemController@editMaintenanceItem');
    Route::get('/admin/maintenance_items/destroy/{maintenance_item_id}', 'Admin\MaintenanceItemController@deleteMaintenanceItem');
    Route::post('/admin/maintenance_items/update', 'Admin\MaintenanceItemController@updateMaintenanceItem');
    Route::post('/admin/maintenance_items/create', 'Admin\MaintenanceItemController@createMaintenanceItem');
});