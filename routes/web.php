<?php

use App\Http\Controllers\MasterFile\CustomerController;
use App\Http\Controllers\MasterFile\ItemController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::get('master-file/customers', [CustomerController::class, 'index'])->name('master-file.customers');
    Route::post('master-file/customers', [CustomerController::class, 'store'])->name('master-file.customers.store');
    Route::put('master-file/customers/{customer}', [CustomerController::class, 'update'])->name('master-file.customers.update');
    Route::delete('master-file/customers/{customer}', [CustomerController::class, 'destroy'])->name('master-file.customers.destroy');

    Route::get('master-file/items', [ItemController::class, 'index'])->name('master-file.items');
    Route::post('master-file/items', [ItemController::class, 'store'])->name('master-file.items.store');
    Route::put('master-file/items/{item}', [ItemController::class, 'update'])->name('master-file.items.update');
    Route::delete('master-file/items/{item}', [ItemController::class, 'destroy'])->name('master-file.items.destroy');

    Route::get('transaksi/order', fn () => inertia('transaksi/Order', [
        'pageSubtitle' => 'Transaksi',
    ]))->name('transaksi.order');
});

require __DIR__.'/settings.php';
