<?php

use Illuminate\Http\Request;

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

Route::post('/v1/users', 'Admin\UserController@apiCreateUser');

Route::post('/v1/users/{user}/score', 'Admin\UserController@apiSetUserScore');
Route::get('/v1/users/{user}/score', 'Admin\UserController@apiGetUserScore');

Route::get('/v1/users/{user}/done_maintenance_items', 'Admin\UserController@apiGetUserDoneMaintenanceItems');
Route::get('/v1/users/{user}/ignored_maintenance_items', 'Admin\UserController@apiGetUserIgnoredMaintenanceItems');
Route::get('/v1/users/{user}/top_three_maintenance_items_today', 'Admin\UserController@apiGetTop3MaintenanceItemsTodayByUser');

Route::get('/v1/sections/{section}/maintenance_items', 'Admin\MaintenanceItemController@apiGetSectionMaintenanceItems');