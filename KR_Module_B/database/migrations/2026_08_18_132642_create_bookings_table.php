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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 10)->unique();
            $table->string('departure_code', 40)->index();
            $table->foreignIdFor(\App\Models\Line::class)->constrained();
            $table->foreignIdFor(\App\Models\Station::class)->constrained();
            $table->date('departure_date')->index();
            $table->time('departure_time');
            $table->string('first_name', 60);
            $table->string('last_name', 60);
            $table->string('email');
            $table->string('phone', 30)->nullable();
            $table->unsignedTinyInteger('seats');
            $table->decimal('fare_cny', 5, 2);
            $table->enum('status', ['confirmed', 'cancelled'])->default('confirmed');
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
