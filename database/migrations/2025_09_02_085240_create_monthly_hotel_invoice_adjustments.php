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
        Schema::create('monthly_hotel_invoice_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('month');
            $table->string('purpose');
            $table->string('type');
            $table->string('source');
            $table->string('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_hotel_invoice_adjustments');
    }
};
