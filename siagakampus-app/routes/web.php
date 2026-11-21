<?php

use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MapController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::controller(MapController::class)->group(function () {
    Route::get('/map', 'index')->name('map.index');       // Merender resources/views/map.blade.php
    Route::get('/map/data', 'data')->name('map.data');   // Endpoint JSON layer spasial OpenLayers
});

// PBI 12: Inventaris Bencana & Sinkronisasi Titik Ikon "BACKPACK"
Route::get('/api/inventories/points', [InventoryController::class, 'getMapPoints'])->name('inventories.points');

Route::prefix('inventaris')->name('inventories.')->group(function () {
    // Bisa dilihat Murid, Admin, dan Dosen
    Route::get('/', [InventoryController::class, 'index'])->name('index');

    // Form Pemakaian di lokasi (ambil atribut)
    Route::post('/{inventory}/pakai', [InventoryController::class, 'storeUsage'])->name('usage.store');

    // Khusus Admin / Dosen (pengecekan role juga dijaga di Controller / Middleware)
    Route::post('/', [InventoryController::class, 'store'])->name('store');
    Route::patch('/items/{item}', [InventoryController::class, 'updateStock'])->name('items.update');
});
