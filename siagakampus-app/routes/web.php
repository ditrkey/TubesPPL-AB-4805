<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapController;

Route::get('/', function () {
    return view('welcome');
});

Route::controller(MapController::class)->group(function () {
    Route::get('/map', 'index')->name('map.index');       // Merender resources/views/map.blade.php
    Route::get('/map/data', 'data')->name('map.data');   // Endpoint JSON layer spasial OpenLayers
});
