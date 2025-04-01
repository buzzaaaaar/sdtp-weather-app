<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trigger Store Reading</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-4xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-4">Store Reading Trigger</h1>
        <p class="mb-4">{{ $message }}</p>
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Readings</h2>
            <ul class="space-y-4">
                @foreach ($readings as $reading)
                    <li class="border-b pb-4">
                        <p><strong>Location:</strong> {{ $reading->location_name }}</p>
                        <p><strong>AQI:</strong> {{ $reading->aqi }}</p>
                        <p><strong>Latitude:</strong> {{ $reading->latitude }}</p>
                        <p><strong>Longitude:</strong> {{ $reading->longitude }}</p>
                        <p><strong>Station Name:</strong> {{ $reading->station_name }}</p>
                        <p><strong>Reading Time:</strong> {{ $reading->reading_time }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</body>
</html>
