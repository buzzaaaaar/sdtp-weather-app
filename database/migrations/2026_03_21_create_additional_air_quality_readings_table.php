<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('additional_air_quality_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('air_quality_reading_id')->constrained()->onDelete('cascade');
            $table->decimal('co', 8, 2)->nullable();  // Carbon Monoxide
            $table->decimal('no2', 8, 2)->nullable(); // Nitrogen Dioxide
            $table->decimal('o3', 8, 2)->nullable();  // Ozone
            $table->decimal('so2', 8, 2)->nullable(); // Sulfur Dioxide
            $table->decimal('pm10', 8, 2)->nullable(); // Particulate Matter ≤10 micrometers
            $table->decimal('pm25', 8, 2)->nullable(); // Particulate Matter ≤2.5 micrometers
            $table->json('forecast_daily')->nullable(); // Store daily forecast data
            $table->timestamp('reading_time');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('additional_air_quality_readings');
    }
}; 