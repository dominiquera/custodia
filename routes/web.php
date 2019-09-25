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

    Route::get('/admin/event_types', 'Admin\EventTypeController@eventTypes')->name('manage-event-types');
    Route::get('/admin/event_types/new', 'Admin\EventTypeController@newEventType');
    Route::get('/admin/event_types/edit/{maintenance_item_id}', 'Admin\EventTypeController@editEventType');
    Route::get('/admin/event_types/destroy/{maintenance_item_id}', 'Admin\EventTypeController@deleteEventType');
    Route::post('/admin/event_types/update', 'Admin\EventTypeController@updateEventType');
    Route::post('/admin/event_types/create', 'Admin\EventTypeController@createEventType');

    Route::get('/admin/monthly_events', 'Admin\MonthlyEventController@monthlyEvents')->name('manage-monthly-events');
    Route::get('/admin/monthly_events/new', 'Admin\MonthlyEventController@newMonthlyEvent');
    Route::get('/admin/monthly_events/edit/{monthly_event_id}', 'Admin\MonthlyEventController@editMonthlyEvent');
    Route::get('/admin/monthly_events/destroy/{monthly_event_id}', 'Admin\MonthlyEventController@deleteMonthlyEvent');
    Route::post('/admin/monthly_events/update', 'Admin\MonthlyEventController@updateMonthlyEvent');
    Route::post('/admin/monthly_events/create', 'Admin\MonthlyEventController@createMonthlyEvent');

    Route::get('/admin/weather_triggers', 'Admin\WeatherTriggerController@weatherTriggers')->name('manage-weather-triggers');
    Route::get('/admin/weather_triggers/new', 'Admin\WeatherTriggerController@newWeatherTrigger');
    Route::get('/admin/weather_triggers/edit/{weather_trigger_id}', 'Admin\WeatherTriggerController@editWeatherTrigger');
    Route::get('/admin/weather_triggers/destroy/{weather_trigger_id}', 'Admin\WeatherTriggerController@deleteWeatherTrigger');
    Route::post('/admin/weather_triggers/update', 'Admin\WeatherTriggerController@updateWeatherTrigger');
    Route::post('/admin/weather_triggers/create', 'Admin\WeatherTriggerController@createWeatherTrigger');
});