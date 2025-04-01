<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdditionalAirQualityReading extends Model
{
    protected $fillable = [
        'air_quality_reading_id',
        'co',
        'no2',
        'o3',
        'so2',
        'pm10',
        'pm25',
        'forecast_daily',
        'reading_time'
    ];

    protected $casts = [
        'reading_time' => 'datetime',
        'forecast_daily' => 'array',
        'co' => 'decimal:2',
        'no2' => 'decimal:2',
        'o3' => 'decimal:2',
        'so2' => 'decimal:2',
        'pm10' => 'decimal:2',
        'pm25' => 'decimal:2'
    ];

    /**
     * Get the main air quality reading that this additional data belongs to.
     */
    public function airQualityReading()
    {
        return $this->belongsTo(AirQualityReading::class);
    }
} 