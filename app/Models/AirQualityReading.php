<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AirQualityReading extends Model
{
    protected $fillable = [
        'aqi',
        'latitude',
        'longitude',
        'location_name',
        'station_name',
        'data_source',
        'reading_time'
    ];

    protected $casts = [
        'reading_time' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    /**
     * Get the additional air quality reading data associated with this reading.
     */
    public function additionalData()
    {
        return $this->hasOne(AdditionalAirQualityReading::class);
    }
} 