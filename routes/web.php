<?php

use App\Livewire\PengantaranTable;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengantaranController;
use App\Http\Controllers\UserManagementController;
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

// Pengantaran Route
Route::get('/pengantaran', PengantaranTable::class);
Route::get('/user-management', [UserManagementController::class, 'index'])->name('users.management');

Route::delete('/users/delete-selected', 
    [UserManagementController::class, 'destroy']
)->name('users.destroy');



// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

// Route::middleware(['auth'])->group(function () {
//     Route::redirect('settings', 'settings/profile');

Volt::route('/inventory', 'inventory')
    ->name('inventory');

require __DIR__ . '/auth.php';
