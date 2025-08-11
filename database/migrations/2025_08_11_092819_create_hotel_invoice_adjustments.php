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
        Schema::create('hotel_invoice_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_invoice_id')
                ->constrained('hotel_invoices')
                ->restrictOnDelete()->cascadeOnUpdate();
            $table->string('purpose');
            $table->string('adjustment_type');
            $table->decimal('amount_bdt', 12,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_invoice_adjustments');
    }
};
