<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StaffOrderController;
use App\Http\Controllers\FoodItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

// after login, send each role to its first page
Route::get('/dashboard', function () {
    $role = auth()->user()->role;

    if ($role === 'staff') {
        return redirect()->route('staff.orders.index');
    }

    if (in_array($role, ['manager', 'owner'])) {
        return redirect()->route('staff.dashboard');
    }

    abort(403, 'Unauthorized role.');
})->middleware(['auth', 'verified'])->name('dashboard');

// profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// ---------- customer (no login needed) ----------
Route::get('/order/{qr_token}', [OrderController::class, 'menu'])->name('order.menu');
Route::post('/order/{qr_token}/add-to-cart', [OrderController::class, 'addToCart'])->name('order.addToCart');
Route::get('/order/{qr_token}/cart-json', [OrderController::class, 'viewCartJson'])->name('order.viewCartJson');
Route::post('/order/{qr_token}/update-cart-quantity', [OrderController::class, 'updateCartQuantity'])->name('order.updateCartQuantity');
Route::post('/order/{qr_token}/remove-from-cart', [OrderController::class, 'removeFromCart'])->name('order.removeFromCart');
Route::post('/order/{qr_token}/checkout', [OrderController::class, 'placeOrder'])->name('order.placeOrder');
Route::get('/order/confirmation/{orderId}', [OrderController::class, 'confirmation'])->name('order.confirmation');

// ---------- staff, manager and owner ----------
Route::middleware(['auth', 'role:staff,manager,owner'])->group(function () {

    // orders
    Route::get('/staff/orders', [StaffOrderController::class, 'index'])->name('staff.orders.index');
    Route::put('/staff/orders/{id}/status', [StaffOrderController::class, 'updateStatus'])->name('staff.orders.updateStatus');
    Route::put('/staff/orders/{id}/paid', [StaffOrderController::class, 'markPaid'])->name('staff.orders.markPaid');
    Route::get('/staff/orders/{id}/print', [StaffOrderController::class, 'printTicket'])->name('staff.orders.print');
    Route::get('/staff/orders/{id}/receipt', [StaffOrderController::class, 'printReceipt'])->name('staff.orders.receipt');
        Route::put('/staff/orders/{order}/cancel', [StaffOrderController::class, 'cancel'])->name('staff.orders.cancel');
    Route::delete('/staff/orders/{order}/items/{item}', [StaffOrderController::class, 'removeItem'])->name('staff.orders.removeItem');

    // tables
    Route::get('/staff/tables', [TableController::class, 'index'])->name('staff.tables.index');
    Route::post('/staff/tables', [TableController::class, 'store'])->name('staff.tables.store');
    Route::delete('/staff/tables/{id}', [TableController::class, 'destroy'])->name('staff.tables.destroy');
    Route::post('/staff/tables/{id}/open', [TableController::class, 'openTable'])->name('staff.tables.open');
    Route::post('/staff/tables/{id}/close', [TableController::class, 'closeTable'])->name('staff.tables.close');
    Route::get('/staff/tables/{id}/print', [TableController::class, 'printQr'])->name('staff.tables.print');

    // food items
    Route::get('/staff/food-items', [FoodItemController::class, 'index'])->name('staff.foodItems.index');
    Route::put('/staff/food-items/{id}/toggle-status', [FoodItemController::class, 'toggleStatus'])->name('staff.foodItems.toggleStatus');
    Route::post('/staff/food-items', [FoodItemController::class, 'store'])->name('staff.foodItems.store');
    Route::put('/staff/food-items/{id}', [FoodItemController::class, 'update'])->name('staff.foodItems.update');
    Route::delete('/staff/food-items/{id}', [FoodItemController::class, 'destroy'])->name('staff.foodItems.destroy');

    // categories
    Route::post('/staff/categories', [CategoryController::class, 'store'])->name('staff.categories.store');
    Route::put('/staff/categories/{id}', [CategoryController::class, 'update'])->name('staff.categories.update');
    Route::delete('/staff/categories/{id}', [CategoryController::class, 'destroy'])->name('staff.categories.destroy');
});

// ---------- manager and owner only ----------
Route::middleware(['auth', 'role:manager,owner'])->group(function () {
    Route::get('/staff/dashboard', [DashboardController::class, 'index'])->name('staff.dashboard');

    Route::get('/staff/accounts', [UserController::class, 'index'])->name('staff.accounts.index');
    Route::post('/staff/accounts', [UserController::class, 'store'])->name('staff.accounts.store');
    Route::put('/staff/accounts/{id}', [UserController::class, 'update'])->name('staff.accounts.update');
    Route::delete('/staff/accounts/{id}', [UserController::class, 'destroy'])->name('staff.accounts.destroy');
});

// ---------- owner only ----------
Route::middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/staff/settings', [SettingController::class, 'edit'])->name('staff.settings.edit');
    Route::put('/staff/settings', [SettingController::class, 'update'])->name('staff.settings.update');
});

require __DIR__.'/auth.php';
