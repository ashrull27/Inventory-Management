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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->enum('transaction_type', ['IN', 'OUT']);
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2); // Store price at transaction time
            $table->text('remarks')->nullable();
            $table->string('reference_number')->nullable();
            $table->timestamp('transaction_time');
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index('product_id');
            $table->index('transaction_type');
            $table->index('transaction_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};