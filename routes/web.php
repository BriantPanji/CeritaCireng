<?php

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

Volt::route('/inventory', 'inventory')
    ->name('inventory');

require __DIR__ . '/auth.php';
