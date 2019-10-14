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


Route::post('/v1/auth', 'Admin\UserController@apiAuthenticateUser');

Route::post('/v1/users/{user}/score', 'Admin\UserController@apiSetUserScore');
Route::get('/v1/users/{user}/score', 'Admin\UserController@apiGetUserScore');

Route::post('/v1/users/{user}/outdoor_spaces', 'Admin\UserController@apiSetOutdoorSpaces');
Route::get('/v1/users/{user}/outdoor_spaces', 'Admin\UserController@apiGetOutdoorSpaces');

Route::post('/v1/users/{user}/driveways', 'Admin\UserController@apiSetDriveways');
Route::get('/v1/users/{user}/driveways', 'Admin\UserController@apiGetDriveways');

Route::post('/v1/users/{user}/home_features', 'Admin\UserController@apiSetHomeFeatures');
Route::get('/v1/users/{user}/home_features', 'Admin\UserController@apiGetHomeFeatures');

Route::post('/v1/users/{user}/mobility_issues', 'Admin\UserController@apiSetMobilityIssues');
Route::get('/v1/users/{user}/mobility_issues', 'Admin\UserController@apiGetMobilityIssues');

Route::get('/v1/users/{user}/done_maintenance_items', 'Admin\UserController@apiGetUserDoneMaintenanceItems');
Route::get('/v1/users/{user}/ignored_maintenance_items', 'Admin\UserController@apiGetUserIgnoredMaintenanceItems');

Route::get('/v1/users/{user}/maintenance_item/{maintenance_item}/done', 'Admin\UserController@apiGetIsMaintenanceItemDone');
Route::get('/v1/users/{user}/maintenance_item/{maintenance_item}/ignored', 'Admin\UserController@apiGetIsMaintenanceItemIgnored');
Route::post('/v1/users/{user}/maintenance_item/{maintenance_item}/done', 'Admin\UserController@apiSetMaintenanceItemDone');
Route::post('/v1/users/{user}/maintenance_item/{maintenance_item}/ignored', 'Admin\UserController@apiSetMaintenanceItemIgnored');


Route::get('/v1/users/{user}/top_three_maintenance_items_today', 'Admin\UserController@apiGetTop3MaintenanceItemsTodayByUser');
Route::get('/v1/users/{user}/section/{section}/top_three_maintenance_items_today', 'Admin\UserController@apiGetTop3MaintenanceItemsTodayByUserAndSection');

Route::get('/v1/sections/{section}/maintenance_items', 'Admin\MaintenanceItemController@apiGetSectionMaintenanceItems');