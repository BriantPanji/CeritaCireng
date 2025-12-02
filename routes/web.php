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

// Attendance/Absensi - Unified route
Route::middleware(['auth'])->group(function () {
    Route::get('/absensi', [AttendanceController::class, 'index'])->name('absensi.index');
    
    // Route untuk attendance tetap ada, redirect ke absensi
    Route::get('/attendance', function () {
        return redirect()->route('absensi.index');
    });
});

// Inventory
Route::get('/inventory', function () {
    return view('inventory');
});

// User Management
Volt::route('/user-management', 'user-management')
    ->name('users.management');

Route::get('/user-details/{id}', \App\Livewire\UserDetails::class)
    ->name('user.details');

// Pengantaran Route
Route::get('/delivery', PengantaranTable::class)->name('delivery.index');
Route::get('/delivery/add', function () {
    return view('delivery-add');
})->name('delivery.add')->middleware('auth');

Route::post('/delivery/{id}/start', [DashboardController::class, 'startDelivery'])->name('delivery.start')->middleware('auth');

// Outlet Management
Volt::route('/outlet-management', 'outlet-management')
    ->name('outlets.management');

// Penerimaan Barang
use App\Livewire\ReceivingTable;

Route::get('/penerimaan-barang', ReceivingTable::class)
    ->name('receiving.index')
    ->middleware('auth');


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

require __DIR__ . '/auth.php';
