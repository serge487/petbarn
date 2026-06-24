<?php

use App\Http\Controllers\Api\SyncController;
use Illuminate\Support\Facades\Route;

/*
| PawsNation storefront sync API. All routes require the shared bearer token.
*/
Route::middleware('sync.token')->prefix('sync')->group(function () {
    Route::get('/branches', [SyncController::class, 'branches'])->name('sync.branches');
    Route::get('/products', [SyncController::class, 'products'])->name('sync.products');
    Route::get('/inventory', [SyncController::class, 'inventory'])->name('sync.inventory');
});
