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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id');
            $table->unsignedBigInteger('user_id'); // peminjam
            $table->unsignedBigInteger('approver_id')->nullable(); // penyetuju
            $table->text('purpose')->nullable();
            $table->dateTime('loan_date');
            $table->dateTime('expected_return_date');
            $table->dateTime('actual_return_date')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected, returned, overdue
            $table->text('notes')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamps();
            
            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('restrict');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('approver_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
