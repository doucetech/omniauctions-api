<?php

use App\Http\Controllers\API\v1\BidController;
use App\Http\Controllers\API\v1\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/my-products', [ProductController::class, 'userProducts']);
    Route::get('/bids', [BidController::class, 'userBids']);

    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::middleware(['auth', 'check.subscription'])->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::post('/products/{productId}/images', [ProductController::class, 'addImages']);
    });
    Route::get('/products/{productId}/next-bids', [BidController::class, 'getNextBidOptions']);
    Route::post('/products/{productId}/bids', [BidController::class, 'placeBid']);

    Route::get('/subscription/required', function () {
        return response()->json([
            'message' => 'Subscription required. Please subscribe to access this content.',
        ], 403);
    })->name('subscription.required');

    Route::get('/subscription/expired', function () {
        return response()->json([
            'message' => 'Your subscription has expired. Please renew your subscription to continue.',
        ], 403);
    })->name('subscription.expired');

});
