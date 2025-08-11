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
        Schema::create('hotel_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')
                ->constrained('hotels')
                ->restrictOnDelete()->cascadeOnUpdate();
            $table->string('inv_no' )->unique();
            $table->date('inv_date' )->nullable();
            $table->decimal('total_amount', 12,2);
            $table->decimal('total_advance', 12,2)->nullable();
            $table->foreignId('currency_id')->nullable()
                ->constrained('currencies')
                ->restrictOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_invoices');
    }
};
