<?php

use App\Http\Controllers\BlockController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\Admin\LotController as AdminLotController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ── Public ────────────────────────────────────────────────
Route::get('/', function () {
    return redirect()->route('login');
});

// ── Authenticated Routes ──────────────────────────────────
Route::middleware('auth')->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Inventory (all authenticated users) ───────────────
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory-pdf', [InventoryController::class, 'exportPdf'])->name('inventory.pdf');
    Route::get('/inventory/{lot}', [InventoryController::class, 'show'])->name('inventory.show');
    Route::get('/api/lots/{lot}/history', [\App\Http\Controllers\InventoryController::class, 'history'])->name('lots.history');

    // ── Blocks (vendedores) ───────────────────────────────
    Route::middleware('role:vendedor')->group(function () {
        Route::post('/blocks', [BlockController::class, 'store'])->name('blocks.store');
        Route::delete('/blocks/{block}', [BlockController::class, 'cancel'])->name('blocks.cancel');
    });

    // ── Supervisor Actions ────────────────────────────────
    Route::middleware('role:supervisor')->group(function () {
        Route::put('/blocks/{block}/extend', [BlockController::class, 'extend'])->name('blocks.extend');
        Route::put('/blocks/{block}/release', [BlockController::class, 'release'])->name('blocks.release');
        Route::put('/lots/{lot}/reserve', [BlockController::class, 'reserve'])->name('lots.reserve');
        Route::put('/lots/{lot}/sell', [BlockController::class, 'sell'])->name('lots.sell');
        Route::put('/lots/{lot}/revert', [BlockController::class, 'revertState'])->name('lots.revert');
    });

    // ── Dashboard (admin, supervisor, control) ────────────
    Route::middleware('role:admin,supervisor,control')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/api/dashboard/inventory', [DashboardController::class, 'inventoryChart'])->name('api.dashboard.inventory');
        Route::get('/api/dashboard/trend', [DashboardController::class, 'trendChart'])->name('api.dashboard.trend');
        Route::get('/api/dashboard/performance', [DashboardController::class, 'performanceChart'])->name('api.dashboard.performance');
        Route::get('/api/dashboard/kpis', [DashboardController::class, 'kpis'])->name('api.dashboard.kpis');
        Route::get('/api/dashboard/expired-report', [DashboardController::class, 'expiredReport'])->name('api.dashboard.expired-report');
    });

    // ── Admin Routes ──────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // Lots CRUD
        Route::resource('lots', AdminLotController::class);
        Route::put('/lots/{lot}/state', [AdminLotController::class, 'updateState'])->name('lots.state');
        Route::get('/lots-import/template', [AdminLotController::class, 'template'])->name('lots.template');
        Route::get('/lots-import', [AdminLotController::class, 'importForm'])->name('lots.import.form');
        Route::post('/lots-import', [AdminLotController::class, 'import'])->name('lots.import');

        // Users CRUD
        Route::resource('users', AdminUserController::class)->except(['show']);

        // System Settings
        Route::get('/settings', function () {
            return view('admin.settings');
        })->name('settings');

        Route::post('/settings', function (\Illuminate\Http\Request $request) {
            $request->validate([
                'trend_start_date' => 'required|date',
                'app_title' => 'nullable|string|max:100',
                'app_logo' => 'nullable|image|max:2048'
            ]);
            
            \App\Models\SystemSetting::set('trend_start_date', $request->trend_start_date);
            
            if ($request->filled('app_title')) {
                \App\Models\SystemSetting::set('app_title', $request->app_title);
            }
            if ($request->hasFile('app_logo')) {
                $path = $request->file('app_logo')->store('logos', 'public');
                \App\Models\SystemSetting::set('app_logo_path', $path);
            }

            return back()->with('success', 'Configuración guardada.');
        })->name('settings.update');
    });
    // PWA Push Subscription
    Route::post('/push/subscribe', [\App\Http\Controllers\PushSubscriptionController::class, 'store'])->name('push.subscribe');
});

require __DIR__.'/auth.php';
