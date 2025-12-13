<?php

use App\Livewire\PengantaranTable;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengantaranController;
use App\Http\Controllers\UserManagementController;
use App\Livewire\UserManagement;
use Livewire\Volt\Volt;
use App\Http\Controllers\AttendanceController;
use App\Livewire\ReceivingTable;
use App\Http\Controllers\StaffController;
use App\Livewire\DailyReportTable;
use App\Http\Controllers\DailyReportController;
use App\Livewire\DailyReportCreate;

// ============================================================================
// PUBLIC ROUTES (No Auth Required)
// ============================================================================

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// ============================================================================
// AUTHENTICATED ROUTES (Auth Required)
// ============================================================================

Route::middleware(['auth'])->group(function () {

    // ------------------------------------------------------------------------
    // Dashboard - All authenticated users
    // ------------------------------------------------------------------------
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Backward compatibility: redirect old staff dashboard to unified dashboard
    Route::get('/staff/dashboard', function () {
        return redirect()->route('dashboard');
    })->name('staff.dashboard');

    // ------------------------------------------------------------------------
    // Attendance/Absensi - All authenticated users
    // ------------------------------------------------------------------------
    Route::get('/absensi', [AttendanceController::class, 'index'])->name('absensi.index');
    Route::get('/attendance', function () {
        return redirect()->route('absensi.index');
    });

    // ------------------------------------------------------------------------
    // Delivery/Pengantaran Routes - dev, admin, inventaris, kurir, staff
    // ------------------------------------------------------------------------
    Route::middleware(['checkrole:dev,admin,inventaris,kurir,staff'])->group(function () {
        Route::get('/delivery', PengantaranTable::class)->name('delivery.index');
        Route::get('/delivery/add', function () {
            return view('delivery-add');
        })->name('delivery.add');
        Route::post('/delivery/{id}/start', [DashboardController::class, 'startDelivery'])->name('delivery.start');
    });

    // ------------------------------------------------------------------------
    // Return Items Routes - dev, admin, inventaris, staff
    // ------------------------------------------------------------------------
    Route::middleware(['checkrole:dev,admin,inventaris,staff'])->group(function () {
        Route::get('/returns', \App\Livewire\ReturnTable::class)->name('returns.index');
    });

    // ------------------------------------------------------------------------
    // Inventory & Stock Routes - dev, admin, inventaris only
    // ------------------------------------------------------------------------
    Route::middleware(['checkrole:dev,admin,inventaris'])->group(function () {
        Route::get('/inventory', function () {
            return view('inventory');
        });
        Volt::route('/inventory', 'inventory')->name('inventory');
        Route::get('/stok', \App\Livewire\StockTable::class)->name('stok.index');
        Route::get('/penerimaan-barang', ReceivingTable::class)->name('receiving.index');
    });

    // ------------------------------------------------------------------------
    // Daily Reports - dev, admin, inventaris, staff
    // ------------------------------------------------------------------------
    Route::get('/daily-reports/create', DailyReportCreate::class)
        ->name('daily-reports.create')
        ->middleware(['checkrole:dev,admin,staff']);

    Route::middleware(['checkrole:dev,admin,inventaris,staff'])->group(function () {
        Route::get('/daily-reports', DailyReportTable::class)->name('daily-reports.index');
        Route::get('/daily-reports/export/excel', [DailyReportController::class, 'export'])->name('daily-reports.export');
        Route::get('/daily-reports/{id}', [DailyReportController::class, 'show'])->name('daily-reports.show');
    });

    // ------------------------------------------------------------------------
    // User Management - dev, admin only
    // ------------------------------------------------------------------------
    Route::middleware(['checkrole:dev,admin'])->group(function () {
        Volt::route('/user-management', 'user-management')->name('users.management');
        Route::get('/user-details/{id}', \App\Livewire\UserDetails::class)->name('user.details');
    });

    // ------------------------------------------------------------------------
    // Outlet Management - dev, admin only
    // ------------------------------------------------------------------------
    Route::middleware(['checkrole:dev,admin'])->group(function () {
        Volt::route('/outlet-management', 'outlet-management')->name('outlets.management');
    });

    // ------------------------------------------------------------------------
    // Staff-specific Routes - staff only
    // ------------------------------------------------------------------------
    Route::middleware(['checkrole:staff'])->group(function () {
        // Konfirmasi barang masuk
        Route::get('/staff/receiving', [StaffController::class, 'receivingForm'])->name('staff.receiving.form');
        Route::post('/staff/receiving', [StaffController::class, 'submitReceiving'])->name('staff.receiving.submit');

        // Laporan kesalahan
        Route::get('/staff/error-report', [StaffController::class, 'errorForm'])->name('staff.error.form');
        Route::post('/staff/error-report', [StaffController::class, 'submitError'])->name('staff.error.submit');
    });

});

// ============================================================================
// AUTH ROUTES (Login, Register, etc.)
// ============================================================================

require __DIR__ . '/auth.php';
