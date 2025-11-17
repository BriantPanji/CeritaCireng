<?php

use App\Http\Controllers\UserManagementController;
use App\Livewire\UserManagement;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\AttendanceController;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');


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


// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

// Route::middleware(['auth'])->group(function () {
//     Route::redirect('settings', 'settings/profile');

Volt::route('/inventory', 'inventory')
    ->name('inventory');

require __DIR__ . '/auth.php';
