<?php

use Illuminate\Support\Facades\Route;

// API routes
Route::get('/api/stats', 'MonitorController@stats')->name('govel-monitor.api.stats');
Route::get('/api/executions', 'MonitorController@executions')->name('govel-monitor.api.executions');
Route::get('/api/executions/{id}', 'MonitorController@show')->where('id', '[0-9]+')->name('govel-monitor.api.show');
Route::get('/api/filters', 'MonitorController@filters')->name('govel-monitor.api.filters');
Route::delete('/api/purge', 'MonitorController@purge')->name('govel-monitor.api.purge');

// SPA catch-all (must be last)
Route::get('/{any?}', 'MonitorController@index')
    ->where('any', '.*')
    ->name('govel-monitor.dashboard');
