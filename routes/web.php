<?php

use App\Http\Controllers\Transaksi\OrderController;
use App\Http\Controllers\MasterFile\CustomerController;
use App\Http\Controllers\MasterFile\ItemController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::get('master-file/customers', [CustomerController::class, 'index'])->name('master-file.customers');
    Route::get('master-file/customers/search', [CustomerController::class, 'search'])->name('master-file.customers.search');
    Route::post('master-file/customers', [CustomerController::class, 'store'])->name('master-file.customers.store');
    Route::put('master-file/customers/{customer}', [CustomerController::class, 'update'])->name('master-file.customers.update');
    Route::delete('master-file/customers/{customer}', [CustomerController::class, 'destroy'])->name('master-file.customers.destroy');

    Route::get('master-file/items', [ItemController::class, 'index'])->name('master-file.items');
    Route::post('master-file/items', [ItemController::class, 'store'])->name('master-file.items.store');
    Route::put('master-file/items/{item}', [ItemController::class, 'update'])->name('master-file.items.update');
    Route::delete('master-file/items/{item}', [ItemController::class, 'destroy'])->name('master-file.items.destroy');

    Route::get('transaksi/order', [OrderController::class, 'index'])->name('transaksi.order');
    Route::get('transaksi/order/items', [OrderController::class, 'searchItems'])->name('transaksi.order.items');
    Route::post('transaksi/order', [OrderController::class, 'store'])->name('transaksi.order.store');
    Route::put('transaksi/order/{order}', [OrderController::class, 'update'])->name('transaksi.order.update');
    Route::delete('transaksi/order/{order}', [OrderController::class, 'destroy'])->name('transaksi.order.destroy');
});

require __DIR__.'/settings.php';
