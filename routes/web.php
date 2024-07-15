<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

Auth::routes();


Route::get('/', [CatalogController::class, 'index'])->name('catalog');
Route::get('/catalog/{id}', [CatalogController::class, 'show'])->name('catalog.show');

// must authenticated
Route::middleware('auth')->group(function () {
    Route::post('/cart', [KeranjangController::class, 'add'])->name('cart.add');
    Route::get('/cart', [KeranjangController::class, 'get'])->name('cart');
    Route::delete('/cart/{id}', [KeranjangController::class, 'delete'])->name('cart.delete');
    Route::put('/cart/{id}', [KeranjangController::class, 'updateQty'])->name('cart.updateQty');

    Route::get('/stock/{id}', [CatalogController::class, 'checkStock'])->name('stock');
    
    Route::get('/checkout/{hash}', [TransaksiController::class, 'checkout'])->name('cart.checkout');
    Route::post('/checkout/{hash}', [TransaksiController::class, 'pay'])->name('checkout');
    Route::post('/handle/{hash}', [TransaksiController::class, 'handle'])->name('handle');

    Route::get('/payment/{type}/{hash}', [TransaksiController::class, 'paymentPage'])->name('payment')->where('type', 'success|failed');

    Route::get('/my-transactions', [TransaksiController::class, 'myTransaction'])->name('my-transactions');
    Route::get('/transaction/{id}', [TransaksiController::class, 'detail'])->name('my-transactions.detail');

    Route::resource('items', ItemController::class);

    Route::get('/order', [OrderController::class, 'index'])->name('order.index');
    Route::get('/order/{id}', [OrderController::class, 'show'])->name('order.show');
    Route::post('/make-shipping', [OrderController::class, 'makeShipping'])->name('order.action');
});

Route::get('/home', [HomeController::class, 'index'])->name('home');
