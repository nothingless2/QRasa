<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PesanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;


// ─────────────────────────────────────────────────────────────
//  GLOBAL RATE LIMIT: 100 req/min/IP applies to ALL routes
//  (applied via throttle named limiter defined in AppServiceProvider)
// ─────────────────────────────────────────────────────────────
Route::middleware(['throttle:global-web'])->group(function () {

    // ─────────────────────────────────────────────────────────
    //  PUBLIC ROUTES (No auth required)
    // ─────────────────────────────────────────────────────────
    Route::get('/', function () {
        return view('welcome');
    });

    // Customer-facing menu catalog (table QR scan entry)
    Route::get('/menus', [MenuController::class, 'show'])->name('menu.show');

    // Customer placing an order and viewing their summary (public from QR)
    Route::post('/pesan', [PesanController::class, 'store'])->name('pesan.store');
    Route::get('/pesan/{pesan}/summary', [PesanController::class, 'show'])->name('pesan.summary');


    // ─────────────────────────────────────────────────────────
    //  AUTHENTICATED ROUTES
    // ─────────────────────────────────────────────────────────
    Route::middleware('auth')->group(function () {

        // ── Profile (all authenticated users) ─────────────────
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // ── Dashboard (admin only) ─────────────────────────────
        Route::middleware('role:admin')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('/dashboard/export-pdf', [DashboardController::class, 'exportPdf'])->name('dashboard.export-pdf');
            Route::get('/dashboard/export-csv', [DashboardController::class, 'exportCsv'])->name('dashboard.export-csv');

            // User management (admin only)
            Route::resource('user', UserController::class);

            // Menu management (admin only — customers read via /menus above)
            Route::resource('menu', MenuController::class)->names([
                'index'   => 'menu.index',
                'create'  => 'menu.create',
                'store'   => 'menu.store',
                'edit'    => 'menu.edit',
                'update'  => 'menu.update',
                'destroy' => 'menu.destroy',
            ])->except('show');

            // Shift history viewer (admin full access)
            Route::get('/shifts', [\App\Http\Controllers\ShiftController::class, 'index'])->name('shifts.index');
            Route::get('/shifts/export-csv', [\App\Http\Controllers\ShiftController::class, 'exportCsv'])->name('shifts.export-csv');

            // Table management (admin only)
            Route::get('meja/print-all', [\App\Http\Controllers\MejaController::class, 'printAll'])->name('meja.printAll');
            Route::get('meja/{meja}/print', [\App\Http\Controllers\MejaController::class, 'print'])->name('meja.print');
            Route::resource('meja', \App\Http\Controllers\MejaController::class);

            // Store Settings (admin only)
            Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
            Route::patch('/settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
        });

        // ── POS & Cashier Operations (admin + cashier) ──────────
        Route::middleware('role:admin,cashier')->group(function () {
            Route::get('/pos', [\App\Http\Controllers\PosController::class, 'index'])->name('pos.index');

            // Shift management
            Route::post('/shift/start', [\App\Http\Controllers\ShiftController::class, 'startShift'])->name('shift.start');
            Route::post('/shift/end', [\App\Http\Controllers\ShiftController::class, 'endShift'])->name('shift.end');

            // Expense tracking
            Route::post('/expense', [\App\Http\Controllers\ExpenseController::class, 'store'])->name('expense.store');
            Route::get('/expense/shift-expenses', [\App\Http\Controllers\ExpenseController::class, 'getShiftExpenses'])->name('expense.shiftExpenses');
        });

        // ── Order Management (admin, cashier, waiter, chef) ─────
        Route::middleware('role:admin,cashier,waiter,chef')->group(function () {
            Route::get('/pesan', [PesanController::class, 'index'])->name('pesan.index');
            Route::patch('/pesan/{pesan}/update-status', [PesanController::class, 'updateStatus'])->name('pesan.updateStatus');
            Route::patch('/pesan/{pesan}/update-status-pembayaran', [PesanController::class, 'updateStatusPembayaran'])->name('pesan.updateStatusPembayaran');
            Route::patch('/pesan/{pesan}/move-table', [PesanController::class, 'moveTable'])->name('pesan.moveTable');
            Route::post('/pesan/{pesan}/split-bill', [PesanController::class, 'splitBill'])->name('pesan.splitBill');
            Route::get('/pesan/{pesan}/struk-kasir', [PesanController::class, 'strukKasir'])->name('pesan.strukKasir');
        });

        // ── KDS / Kitchen (admin + chef) ─────────────────────────
        Route::middleware('role:admin,chef')->group(function () {
            Route::get('/kds', [\App\Http\Controllers\KdsController::class, 'index'])->name('kds.index');
            Route::get('/api/kds/orders', [\App\Http\Controllers\KdsController::class, 'getActiveOrders']);
            Route::post('/api/kds/update/{id}', [\App\Http\Controllers\KdsController::class, 'updateStatus']);
        });

    }); // end auth

}); // end throttle:global-web

require __DIR__ . '/auth.php';
