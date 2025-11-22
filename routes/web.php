<?php

use App\Livewire\PengantaranTable;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengantaranController;
use App\Http\Controllers\UserManagementController;
use App\Livewire\UserManagement;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\AttendanceController;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');


Route::get('/absensi', [AttendanceController::class, 'index'])
->name('absensi.index');



Route::get('/inventory-add-item', function () {
    return view('inventory-add-item');
});

Route::get('/inventory', function () {
    return view('inventory');
});

    
Volt::route('/user-management', 'user-management')
    ->name('users.management');
// Pengantaran Route
Route::get('/pengantaran', PengantaranTable::class);
//Route::get('/user-management', [UserManagementController::class, 'index'])->name('users.management');


use App\Livewire\ReceivingTable;

Route::get('/penerimaan-barang', ReceivingTable::class)
    ->name('receiving.index')
    ->middleware('auth');
Route::middleware(['auth'])->group(function () {
    Volt::route('/attendance', 'attendance');
});
    


// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

// Route::middleware(['auth'])->group(function () {
//     Route::redirect('settings', 'settings/profile');

Volt::route('/inventory', 'inventory')
    ->name('inventory');

require __DIR__ . '/auth.php';
