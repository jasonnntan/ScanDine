<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StaffOrderController;
use App\Http\Controllers\FoodItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TableController;
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

// food crud
Route::get('/staff/food-items/create', [FoodItemController::class, 'create'])->name('staff.foodItems.create');
Route::post('/staff/food-items', [FoodItemController::class, 'store'])->name('staff.foodItems.store');
Route::get('/staff/food-items/{id}/edit', [FoodItemController::class, 'edit'])->name('staff.foodItems.edit');
Route::put('/staff/food-items/{id}', [FoodItemController::class, 'update'])->name('staff.foodItems.update');
Route::delete('/staff/food-items/{id}', [FoodItemController::class, 'destroy'])->name('staff.foodItems.destroy');

// cat crud
Route::get('/staff/categories', [CategoryController::class, 'index'])->name('staff.categories.index');
Route::get('/staff/categories/create', [CategoryController::class, 'create'])->name('staff.categories.create');
Route::post('/staff/categories', [CategoryController::class, 'store'])->name('staff.categories.store');
Route::get('/staff/categories/{id}/edit', [CategoryController::class, 'edit'])->name('staff.categories.edit');
Route::put('/staff/categories/{id}', [CategoryController::class, 'update'])->name('staff.categories.update');
Route::delete('/staff/categories/{id}', [CategoryController::class, 'destroy'])->name('staff.categories.destroy');


// manger and owner role
Route::middleware(['auth', 'role:manager,owner'])->group(function () {
    Route::get('/staff/tables', [\App\Http\Controllers\TableController::class, 'index'])->name('staff.tables.index');
    Route::post('/staff/tables/{id}/open', [\App\Http\Controllers\TableController::class, 'openTable'])->name('staff.tables.open');
    Route::post('/staff/tables/{id}/close', [\App\Http\Controllers\TableController::class, 'closeTable'])->name('staff.tables.close');
    Route::get('/staff/accounts', [UserController::class, 'index'])->name('staff.accounts.index');
    Route::get('/staff/accounts/create', [UserController::class, 'create'])->name('staff.accounts.create');
    Route::post('/staff/accounts', [UserController::class, 'store'])->name('staff.accounts.store');
    Route::delete('/staff/accounts/{id}', [UserController::class, 'destroy'])->name('staff.accounts.destroy');
    Route::get('/staff/dashboard', [DashboardController::class, 'index'])->name('staff.dashboard');
    Route::get('/staff/orders/{id}/print', [StaffOrderController::class, 'printTicket'])->name('staff.orders.print');
    Route::get('/staff/tables/{id}/print', [TableController::class, 'printQr'])->name('staff.tables.print');
});





require __DIR__.'/auth.php';

