<?php

namespace App\Providers;

use App\Rules\ItemBelongToClient;
use App\Rules\PriceFilter;
use App\Rules\ProductBelongToStore;
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
        Validator::extend('product_belong_to_store', function () {
            return ProductBelongToStore::passes(request()->product_id);
        });
        Validator::extend('item_belong_to_client', function () {
            return ItemBelongToClient::passes(request()->item_id);
        });
    }
}
