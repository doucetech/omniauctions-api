<?php

namespace App\Providers;

use App\Interfaces\Bids\BidRepositoryInterface;
use App\Interfaces\Products\ProductRepositoryInterface;
use App\Repositories\Bids\BidRepository;
use App\Repositories\Products\ProductRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepository::class
        );
        $this->app->bind(BidRepositoryInterface::class, BidRepository::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
