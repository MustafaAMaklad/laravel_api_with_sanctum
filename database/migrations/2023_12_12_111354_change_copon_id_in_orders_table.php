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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['copon_id']);
            $table->dropColumn('copon_id');
            $table->unsignedBigInteger('coupon_id')->nullable()->after('is_copon_applied');
            $table->foreign('coupon_id')->references('id')->on('coupons');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('copon_id')->nullable()->after('is_copon_applied');
            $table->foreign('copon_id')->references('id')->on('copons');
            $table->dropForeign(['coupon_id']);
            $table->dropColumn('coupon_id');
        });
    }
};
