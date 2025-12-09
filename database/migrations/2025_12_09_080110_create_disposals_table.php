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
        Schema::create('disposals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id');
            $table->unsignedBigInteger('requested_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->string('reason'); // damaged, lost, obsolete, end_of_life
            $table->text('description')->nullable();
            $table->decimal('estimated_residual_value', 15, 2)->default(0);
            $table->decimal('actual_residual_value', 15, 2)->nullable();
            $table->date('disposal_date')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected, completed
            $table->text('notes')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamps();
            
            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disposals');
    }
};
