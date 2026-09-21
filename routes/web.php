<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StaffOrderController;
use App\Http\Controllers\FoodItemController;
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

// menu
Route::get('/order/{qr_token}', [OrderController::class, 'menu'])->name('order.menu');
Route::post('/order/{qr_token}/add-to-cart', [OrderController::class, 'addToCart'])->name('order.addToCart');
Route::get('/order/{qr_token}/cart', [OrderController::class,'viewCart'])->name('order.viewCart');
Route::post('/order/{qr_token}/checkout', [OrderController::class, 'placeOrder'])->name('order.placeOrder');
Route::get('/order/confirmation/{orderId}', [OrderController::class, 'confirmation'])->name('order.confirmation');

//role
Route::middleware(['auth', 'role:staff,manager,owner'])->group(function () {
    Route::get('/staff/orders', [StaffOrderController::class, 'index'])->name('staff.orders.index');
});

Route::put('/staff/orders/{id}/status', [StaffOrderController::class, 'updateStatus'])->name('staff.orders.updateStatus');
Route::put('/staff/orders/{id}/paid', [StaffOrderController::class, 'markPaid'])->name('staff.orders.markPaid');

Route::get('/staff/food-items', [FoodItemController::class, 'index'])->name('staff.foodItems.index');
Route::put('/staff/food-items/{id}/toggle-status', [FoodItemController::class, 'toggleStatus'])->name('staff.foodItems.toggleStatus');

// crud
Route::get('/staff/food-items/create', [FoodItemController::class, 'create'])->name('staff.foodItems.create');
Route::post('/staff/food-items', [FoodItemController::class, 'store'])->name('staff.foodItems.store');
Route::get('/staff/food-items/{id}/edit', [FoodItemController::class, 'edit'])->name('staff.foodItems.edit');
Route::put('/staff/food-items/{id}', [FoodItemController::class, 'update'])->name('staff.foodItems.update');
Route::delete('/staff/food-items/{id}', [FoodItemController::class, 'destroy'])->name('staff.foodItems.destroy');

require __DIR__.'/auth.php';

