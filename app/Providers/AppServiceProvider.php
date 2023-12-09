<?php

namespace App\Providers;

use App\Rules\PriceFilter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Validator::extend('price_filter', function () {
            return PriceFilter::passes();
        });
    }
}
