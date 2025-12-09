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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id');
            $table->unsignedBigInteger('from_user_id')->nullable();
            $table->unsignedBigInteger('to_user_id')->nullable();
            $table->unsignedBigInteger('from_location_id');
            $table->unsignedBigInteger('to_location_id');
            $table->unsignedBigInteger('requested_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->dateTime('transfer_date');
            $table->text('reason')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected, completed
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->foreign('from_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('to_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('from_location_id')->references('id')->on('locations')->onDelete('restrict');
            $table->foreign('to_location_id')->references('id')->on('locations')->onDelete('restrict');
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
