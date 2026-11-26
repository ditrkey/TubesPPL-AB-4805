<?php

use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MapController;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth', 'role:admin|superadmin'])->group(function () {
    Route::controller(MapController::class)->group(function () {
        Route::get('/map', 'index')->name('map');
        Route::get('/map/data', 'data')->name('map.data');
    });

    // Inventaris (index, store, update stok, pemakaian, dan titik peta)
    Route::get('/inventaris', [InventoryController::class, 'index'])->name('inventories.index');
    Route::post('/inventaris', [InventoryController::class, 'store'])->name('inventories.store');
    Route::patch('/inventaris/items/{item}', [InventoryController::class, 'updateStock'])->name('inventories.items.update');
    Route::post('/inventaris/{inventory}/usage', [InventoryController::class, 'storeUsage'])->name('inventories.usage.store');
    Route::get('/inventaris/map-points', [InventoryController::class, 'getMapPoints'])->name('inventories.map-points');

    // Admin Dashboard & Reports
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function (Request $request) {
            $stats = [
                'total' => Report::count(),
                'pending' => Report::where('status', 'pending')->count(),
                'processed' => Report::where('status', 'processed')->count(),
                'resolved' => Report::where('status', 'resolved')->count(),
            ];

            $categories = collect();

            $reports = Report::query()
                ->when($request->filled('status') && $request->status !== 'all', function ($q) use ($request) {
                    $q->where('status', $request->status);
                })
                ->when($request->filled('search'), function ($q) use ($request) {
                    $search = $request->search;
                    $q->where(function ($sub) use ($search) {
                        $sub->where('location', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    });
                })
                ->latest()
                ->paginate(10)
                ->withQueryString();

            $filters = $request->only(['search', 'category', 'status']);

            return view('admin.dashboard', compact('stats', 'categories', 'reports', 'filters'));
        })->name('dashboard');

        Route::patch('reports/{report}/status', [ReportController::class, 'updateStatus'])
            ->name('reports.update-status');

        Route::resource('reports', ReportController::class);
    });
});