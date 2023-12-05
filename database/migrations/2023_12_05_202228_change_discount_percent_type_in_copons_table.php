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
        Schema::table('copons', function (Blueprint $table) {
            $table->float('discount_percent')->change();
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('copons', function (Blueprint $table) {
            $table->decimal('discount_percent', 3, 2)->nullable(false)->change();
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->string('description')->nullable();
        });
    }
};
