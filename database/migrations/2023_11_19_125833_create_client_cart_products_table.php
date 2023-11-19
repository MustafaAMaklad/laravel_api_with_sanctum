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
        Schema::create('client_cart_products', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients')
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->unsignedBigInteger('cart_id');
            $table->foreign('cart_id')->references('id')->on('carts')
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->integer('cart_product_quantity')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_cart_products');
    }
};
