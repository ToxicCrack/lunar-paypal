<?php

use Illuminate\Support\Facades\Route;
use Lancodev\LunarPaypal\Http\Controllers\OrdersController;

Route::prefix('lunar-paypal')->group(function () {
    Route::post('/orders', [OrdersController::class, 'create'])->name('lunar-paypal.orders.create');

    Route::post('/orders/{order_id}/capture', [OrdersController::class, 'capture'])->name('lunar-paypal.orders.capture');
});
