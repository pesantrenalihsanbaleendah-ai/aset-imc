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
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id');
            $table->unsignedBigInteger('requested_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->date('maintenance_date')->nullable();
            $table->string('type')->default('preventive'); // preventive, corrective, emergency
            $table->text('description');
            $table->text('findings')->nullable();
            $table->text('actions_taken')->nullable();
            $table->decimal('cost', 15, 2)->default(0);
            $table->string('status')->default('pending'); // pending, scheduled, completed, cancelled
            $table->string('document_path')->nullable();
            $table->timestamps();
            
            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
