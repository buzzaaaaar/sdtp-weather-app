<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('air_quality_readings', function (Blueprint $table) {
            $table->id();
            $table->integer('aqi');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('location_name');
            $table->string('station_name');
            $table->string('data_source')->default('WAQI API');
            $table->timestamp('reading_time');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('air_quality_readings');
    }
}; 