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
        Schema::table('asset_categories', function (Blueprint $table) {
            $table->string('depreciation_method')->nullable()->change();
            $table->integer('depreciation_years')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_categories', function (Blueprint $table) {
            $table->string('depreciation_method')->nullable(false)->default('straight_line')->change();
            $table->integer('depreciation_years')->nullable(false)->default(5)->change();
        });
    }
};
