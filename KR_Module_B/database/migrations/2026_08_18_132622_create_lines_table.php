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
        Schema::create('lines', function (Blueprint $table) {
            $table->id();
            $table->string('code', 4)->unique();
            $table->string('name');
            $table->foreignId('station_a_id')->constrained('stations');
            $table->foreignId('station_b_id')->constrained('stations');
            $table->unsignedSmallInteger('seat_capacity');
            $table->unsignedSmallInteger('crossing_minutes');
            $table->decimal('fare_cny', 5, 2);
            $table->enum('status', ['active', 'suspended'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lines');
    }
};
