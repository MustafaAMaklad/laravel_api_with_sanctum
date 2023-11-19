<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('client_wishlist_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients')
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->unsignedBigInteger('wishlist_id');
            $table->foreign('wishlist_id')->references('id')->on('wishlists')
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')
                ->cascadeOnDelete()->cascadeOnUpdate();



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_wishlist_products');
    }
};
