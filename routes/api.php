<?php

use App\Http\Controllers\API\v1\BidController;
use App\Http\Controllers\API\v1\ProductController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::post('/products/{productId}/images', [ProductController::class, 'addImages']);
    Route::get('/products/{productId}/next-bids', [BidController::class, 'getNextBidOptions']);
    Route::post('/products/{productId}/bids', [BidController::class, 'placeBid']);

    Route::get('subscription', [SubscriptionController::class, 'show']);
    Route::post('subscription', [SubscriptionController::class, 'renew']);

});
