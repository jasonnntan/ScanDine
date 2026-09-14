<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/owner-test', function () {
    return 'Welcome, owner! You passed the role check.';
})->middleware(['auth', 'role:owner'])->name('owner-test');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// menu this dont understand
Route::get('/order/{qr_token}', [OrderController::class, 'menu'])->name('order.menu');
Route::post('/order/{qr_token}/add-to-cart', [OrderController::class, 'addToCart'])->name('order.addToCart');

require __DIR__.'/auth.php';

