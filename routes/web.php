<?php

use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/inventory-add-item', function () {
    return view('inventory-add-item');
});
Route::get('/inventory', function () {
    return view('inventory');
});

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
