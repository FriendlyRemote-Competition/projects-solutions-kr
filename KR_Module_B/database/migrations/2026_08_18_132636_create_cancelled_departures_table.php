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
        Schema::create('cancelled_departures', function (Blueprint $table) {
            $table->id();
            $table->string('departure_code', 40)->unique();
            $table->foreignIdFor(\App\Models\Line::class)->constrained();
            $table->foreignIdFor(\App\Models\Station::class)->constrained();
            $table->date('departure_date');
            $table->time('departure_time');
            $table->string('reason', 200)->nullable();
            $table->timestamp('cancelled_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cancelled_departures');
    }
};
