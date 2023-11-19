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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->nullable(false);

            $table->unsignedBigInteger('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('clients')
                ->nullOnDelete()->cascadeOnUpdate();

            $table->unsignedBigInteger('store_id')->nullable();
            $table->foreign('store_id')->references('id')->on('stores')
                ->nullOnDelete()->cascadeOnUpdate();

            $table->decimal('total_price', 10, 2)->nullable(false);
            $table->boolean('is_copon_applied')->default(false);
            $table->string('copon_code')->nullable();
            $table->decimal('copon_discount_percent', 3, 2)->nullable();
            $table->decimal('total_price_after_copon_applied', 10, 2)->nullable();

            $table->enum('rating', [1, 2, 3, 4, 5])->default(null);
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
