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
            $table->bigInteger('obeo_sl')->unique();
            $table->foreignId('status_id')
                ->constrained('reservation_statuses')
                ->restrictOnDelete()->cascadeOnUpdate();
            $table->string('reservation_no' )->unique();
            $table->date('check_in' );
            $table->date('check_out' );
            $table->date('reservation_date' );
            $table->foreignId('hotel_id')
                ->constrained('hotels')
                ->restrictOnDelete()->cascadeOnUpdate();
            $table->string('guest_name',150);
            $table->foreignId('rate_id')
                ->constrained('rates')
                ->restrictOnDelete()->cascadeOnUpdate();
            $table->decimal('total_advance', 10,2)->nullable();
            $table->foreignId('currency_id')->nullable()
                ->constrained('currencies')
                ->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('source_id')
                ->constrained('sources')
                ->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('payment_method_id')
                ->constrained('payment_methods')
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
