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
        Schema::dropIfExists('client_copon_uses');
        Schema::dropIfExists('copons');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('copons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable(false);
            $table->integer('usage_number')->unsigned()->nullable(false);
            $table->decimal('discount_percent', 3, 2)->nullable(false);
            $table->timestamps();
        });
        Schema::create('client_copon_uses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedBigInteger('copon_id');
            $table->foreign('copon_id')->references('id')->on('copons')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }
};
