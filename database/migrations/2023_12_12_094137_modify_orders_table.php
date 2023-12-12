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
            $table->dropColumn('copon_code');
            $table->dropColumn('copon_discount_percent');

            $table->unsignedBigInteger('copon_id')->nullable()->after('is_copon_applied');
            $table->foreign('copon_id')->references('id')->on('copons');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('copon_code')->nullable();
            $table->decimal('copon_discount_percent', 3, 2)->nullable();
            $table->dropForeign(['copon_id']);
            $table->dropColumn('copon_id');
        });
    }
};
