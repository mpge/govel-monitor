<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'MonitorController@dashboard')->name('govel-monitor.dashboard');
Route::get('/tasks', 'MonitorController@tasks')->name('govel-monitor.tasks');
Route::get('/tasks/{id}', 'MonitorController@show')->name('govel-monitor.task.show');
Route::get('/api/stats', 'MonitorController@stats')->name('govel-monitor.api.stats');
Route::delete('/purge', 'MonitorController@purge')->name('govel-monitor.purge');
