<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Station;

class StationsTableSeeder extends Seeder
{
    public function run()
    {
        $stations = [
            [
                'name' => 'Pannipitiya Road, Pelawatte, Sri Lanka',
                'location' => 'Pelawatte',
                'latitude' => 6.8721,
                'longitude' => 79.9207,
                'status' => 'active',
            ],
            [
                'name' => 'Colombo, Sri Lanka',
                'location' => 'Colombo',
                'latitude' => 6.9271,
                'longitude' => 79.8612,
                'status' => 'active',
            ],
            [
                'name' => 'Borella North, Sri Lanka',
                'location' => 'Borella',
                'latitude' => 6.9121,
                'longitude' => 79.8812,
                'status' => 'active',
            ],
            [
                'name' => 'Battaramulla, Sri Lanka',
                'location' => 'Battaramulla',
                'latitude' => 6.8982,
                'longitude' => 79.9182,
                'status' => 'active',
            ],
        ];

        foreach ($stations as $station) {
            Station::create($station);
        }
    }
}
