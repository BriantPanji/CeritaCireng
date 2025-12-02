<?php

use App\Livewire\PengantaranTable;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengantaranController;
use App\Http\Controllers\UserManagementController;
use App\Livewire\UserManagement;
use Livewire\Volt\Volt;
use App\Http\Controllers\AttendanceController;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');


Route::get('/absensi', [AttendanceController::class, 'index'])
    ->name('absensi.index');

Route::get('/inventory', function () {
    return view('inventory');
});


Volt::route('/user-management', 'user-management')
    ->name('users.management');

Route::get('/user-details/{id}', \App\Livewire\UserDetails::class)
    ->name('user.details');

// Pengantaran Route
Route::get('/delivery', PengantaranTable::class)->name('delivery.index');
Route::get('/delivery/add', function () {
    return view('delivery-add');
})->name('delivery.add')->middleware('auth');

//Route::get('/user-management', [UserManagementController::class, 'index'])->name('users.management');

Volt::route('/outlet-management', 'outlet-management')
    ->name('outlets.management');


use App\Livewire\ReceivingTable;

Route::get('/penerimaan-barang', ReceivingTable::class)
    ->name('receiving.index')
    ->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Volt::route('/attendance', 'attendance');
});

use App\Http\Controllers\StaffController;

Route::middleware(['auth', 'checkrole:staff'])->group(function () {
    Route::get('/staff/dashboard', [StaffController::class, 'dashboard'])->name('staff.dashboard');

    // Konfirmasi barang masuk
    Route::get('/staff/receiving/{id}', [StaffController::class, 'receivingForm'])->name('staff.receiving.form');
    Route::post('/staff/receiving/{id}', [StaffController::class, 'submitReceiving'])->name('staff.receiving.submit');

    // Laporan kesalahan
    Route::get('/staff/error-report/{id}', [StaffController::class, 'errorForm'])->name('staff.error.form');
    Route::post('/staff/error-report/{id}', [StaffController::class, 'submitError'])->name('staff.error.submit');
});


// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

// Route::middleware(['auth'])->group(function () {
//     Route::redirect('settings', 'settings/profile');

Volt::route('/inventory', 'inventory')
    ->name('inventory');

Route::get('/stok', \App\Livewire\StockTable::class)
    ->name('stok.index')
    ->middleware('auth');

// Daily Outlet Reports
use App\Livewire\DailyReportTable;
use App\Http\Controllers\DailyReportController;
use App\Livewire\DailyReportCreate;

Route::get('/daily-reports/create', DailyReportCreate::class)->name('daily-reports.create')->middleware(['auth', 'checkrole:dev,admin,staff']);
// View and export reports - only admin and dev
Route::middleware(['auth', 'checkrole:dev,admin'])->group(function () {
    Route::get('/daily-reports', DailyReportTable::class)->name('daily-reports.index');
    Route::get('/daily-reports/export/excel', [DailyReportController::class, 'export'])->name('daily-reports.export');
    Route::get('/daily-reports/{id}', [DailyReportController::class, 'show'])->name('daily-reports.show');
});

// Create report - staff with outlet OR admin/dev (can choose outlet)

require __DIR__ . '/auth.php';
