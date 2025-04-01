<?php

namespace App\Http\Controllers;

use App\Models\AirQualityReading;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class AirQualityController extends Controller
{
    private $locations = [
        "Colombo, Sri Lanka" => [6.9271, 79.8612],
        "Rajagiriya, Sri Lanka" => [6.9090, 79.8960],
        "Kirulapana, Sri Lanka" => [6.8782, 79.8764],
        "Maradana, Sri Lanka" => [6.9287, 79.8639]
    ];

    public function captureAtmosphericMetrics()
    {
        $results = [];
        $waqiApiKey = config('services.waqi.key');

        foreach ($this->locations as $location => $coordinates) {
            [$lat, $lng] = $coordinates;

            // Get AQI data from WAQI API
            $waqiResponse = Http::withoutVerifying()->get("https://api.waqi.info/feed/geo:$lat;$lng/", [
                'token' => $waqiApiKey,
            ]);

            if (!$waqiResponse->successful()) {
                continue;
            }

            $waqiData = $waqiResponse->json();
            $baseAqi = $waqiData['data']['aqi'] ?? null;

            if (!$baseAqi) {
                continue;
            }

            $simulatedAqi = $this->generateDynamicAQI($baseAqi);

            $reading = AirQualityReading::create([
                'aqi' => $simulatedAqi,
                'latitude' => $lat,
                'longitude' => $lng,
                'location_name' => $location,
                'station_name' => $waqiData['data']['city']['name'] ?? $location,
                'reading_time' => Carbon::now(),
            ]);

            $results[] = $reading;
        }

        return response()->json([
            "message" => "Readings stored successfully",
            "data" => $results,
            "count" => count($results)
        ]);
    }

    private function generateDynamicAQI(int $baseValue): int
    {
        $fluctuationRange = $baseValue * 0.15;
        $adjustedValue = $baseValue + mt_rand(-$fluctuationRange, $fluctuationRange);
        return max(0, $adjustedValue);
    }

    public function retrieveAtmosphericData()
    {
        $readings = AirQualityReading::query()
            ->where('reading_time', '>=', Carbon::now()->subHours(24))
            ->orderBy('reading_time', 'asc')
            ->get()
            ->groupBy('location_name');

        return response()->json([
            'data' => $readings,
            'last_updated' => Carbon::now()->toDateTimeString()
        ]);
    }

    public function initiateDataCapture()
    {
        $response = $this->captureAtmosphericMetrics();
        $data = $response->getData();

        return view('trigger-store-reading', [
            'message' => $data->message,
            'readings' => $data->data,
            'count' => $data->count
        ]);
    }
}
