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
        Schema::create('hotel_invoice_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_invoice_id')
                ->constrained('hotel_invoices')
                ->restrictOnDelete()->cascadeOnUpdate();
            $table->string('room_name');
            $table->integer('total_room');
            $table->decimal('total_price', 12,2);
            $table->foreignId('currency_id')
                ->constrained('currencies')
                ->restrictOnDelete()->cascadeOnUpdate();
            $table->decimal('exchange_rate', 12,2);
            $table->string('commission_type');
            $table->decimal('commission_value', 12,2);
            $table->decimal('hotel_given_price', 12,2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_invoice_rooms');
    }
};
