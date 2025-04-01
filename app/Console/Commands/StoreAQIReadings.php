<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\AirQualityController;

class StoreAQIReadings extends Command
{
    protected $signature = 'aqi:store';
    protected $description = 'Store AQI readings for all locations';

    public function handle()
    {
        $controller = new AirQualityController();
        $result = $controller->storeReading();
        $this->info('AQI readings stored successfully');
    }
} 