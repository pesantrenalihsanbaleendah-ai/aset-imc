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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code')->unique();
            $table->string('name');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('responsible_user_id')->nullable();
            $table->text('description')->nullable();
            $table->decimal('acquisition_price', 15, 2)->default(0);
            $table->decimal('book_value', 15, 2)->default(0);
            $table->string('condition')->default('good'); // good, acceptable, poor
            $table->string('status')->default('active'); // active, maintenance, damaged, disposed
            $table->date('acquisition_date')->nullable();
            $table->date('warranty_until')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('qr_code')->nullable();
            $table->text('specification')->nullable();
            $table->string('photo_path')->nullable();
            $table->timestamps();
            
            $table->foreign('category_id')->references('id')->on('asset_categories')->onDelete('restrict');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('restrict');
            $table->foreign('responsible_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
