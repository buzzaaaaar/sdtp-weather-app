<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AirViz | Air Quality Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD95OAdYDDrJjBBqpUqm7vVHchMQiGppBQ"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
            color: #e2e8f0;
        }

        .dashboard-card {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            transition: all 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .sensor-toggle {
            position: relative;
            width: 80px;
            height: 40px;
            appearance: none;
            background: #334155;
            border-radius: 20px;
            transition: all 0.3s;
            cursor: pointer;
            outline: none;
        }

        .sensor-toggle:checked {
            background: #3b82f6;
        }

        .sensor-toggle::before {
            content: '';
            position: absolute;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: white;
            top: 4px;
            left: 4px;
            transition: all 0.3s;
            transform: scale(1.1);
        }

        .sensor-toggle:checked::before {
            left: 44px;
        }

        .data-pill {
            background: rgba(30, 41, 59, 0.5);
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .nav-link {
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: #3b82f6;
            transition: width 0.3s;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .active-nav::after {
            width: 100%;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .floating {
            animation: float 6s ease-in-out infinite;
        }

        .fade-in {
            opacity: 0;
            transform: translateY(20px);
        }

        #map {
            height: 400px;
            width: 100%;
            border-radius: 16px;
            overflow: hidden;
        }

        .custom-marker {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .custom-marker:hover {
            transform: scale(1.2);
        }

        .notification-card {
            transition: all 0.3s ease;
        }

        .notification-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .modal-overlay {
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            transform: translateY(20px);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .modal-open .modal-content {
            transform: translateY(0);
            opacity: 1;
        }

        .aqi-indicator {
            transition: all 0.3s ease;
        }

        .aqi-indicator:hover {
            transform: scale(1.1);
        }

        .admin-badge {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }

        .admin-controls {
            border-left: 2px solid rgba(59, 130, 246, 0.3);
            padding-left: 1rem;
            margin-left: 1rem;
        }
    </style>
</head>

<body>
    <div class="min-h-screen">
        <div class="fixed inset-0 -z-10 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/20 to-cyan-900/10"></div>
            <div class="absolute top-0 right-0 w-1/3 h-1/3 bg-blue-500/10 rounded-full filter blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-1/3 h-1/3 bg-cyan-500/10 rounded-full filter blur-3xl"></div>
        </div>

        <nav class="px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center floating">
                    <i class="fas fa-wind text-white"></i>
                </div>
                <span
                    class="text-xl font-bold bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">AirViz</span>
            </div>

            <div class="hidden md:flex items-center space-x-8">
                <a href="/" class="nav-link active-nav font-medium">Dashboard</a>
                <a href="/analytics" class="nav-link">Analytics</a>
                @guest
                <a href="/login" class="nav-link">Login</a>
                <a href="/register"
                    class="px-4 py-2 bg-blue-500/10 text-blue-400 rounded-lg font-medium hover:bg-blue-500/20 transition">Register</a>
                @else
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center">
                            <span class="text-sm text-white">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <span class="text-slate-300">{{ Auth::user()->name }}</span>
                    </div>
                    @if(Auth::user()->is_admin)
                    <span class="admin-badge text-xs font-medium px-2.5 py-0.5 rounded-full">Admin</span>
                    @endif
                    <form method="POST" action="/logout" class="inline">
                        @csrf
                        <button type="submit" class="text-slate-400 hover:text-slate-200 transition">Logout</button>
                    </form>
                </div>
                @endguest
            </div>

            <button class="md:hidden text-slate-300">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </nav>

        <div class="container mx-auto px-4 py-6">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-white">Air Quality Dashboard</h1>
                    <p class="text-slate-400">Real-time air quality monitoring and analytics</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-slate-400">Live Data</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="dataToggle" class="sensor-toggle">
                        </label>
                        <span class="text-sm text-slate-400">Simulation</span>
                    </div>
                    <div class="data-pill">
                        <i class="fas fa-clock text-xs"></i>
                        <span id="currentTime">Loading...</span>
                    </div>
                </div>
            </div>

            @if(Auth::check() && Auth::user()->is_admin)
            <div class="dashboard-card p-6 mb-6">
                <h3 class="font-semibold mb-4 flex items-center">
                    <i class="fas fa-shield-alt mr-2 text-blue-400"></i>
                    Admin Controls
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <button id="refreshData"
                        class="bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 px-4 py-2 rounded-lg transition flex items-center justify-center">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Force Data Refresh
                    </button>
                    <button id="manageUsers"
                        class="bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 px-4 py-2 rounded-lg transition flex items-center justify-center">
                        <i class="fas fa-users mr-2"></i>
                        Manage Users
                    </button>
                    <button id="systemSettings"
                        class="bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 px-4 py-2 rounded-lg transition flex items-center justify-center">
                        <i class="fas fa-cog mr-2"></i>
                        System Settings
                    </button>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div class="dashboard-card p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-slate-400">Current AQI</p>
                            <h2 class="text-4xl font-bold mt-2" id="currentAqi">--</h2>
                            <p class="text-sm mt-1" id="aqiStatus">Loading...</p>
                        </div>
                        <div class="w-14 h-14 rounded-full flex items-center justify-center" id="aqiStatusIcon">
                            <i class="fas fa-question text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-6 chart-container">
                        <canvas id="miniTrendChart"></canvas>
                    </div>
                </div>

                <div class="dashboard-card p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold">Worst Location</h3>
                        <div class="w-10 h-10 rounded-full flex items-center justify-center" id="worstAqiIcon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-2xl font-bold" id="worstLocation">--</p>
                        <p class="text-sm text-slate-400" id="worstLocationAqi">AQI: --</p>
                    </div>
                </div>

                <div class="dashboard-card p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold">Best Location</h3>
                        <div class="w-10 h-10 rounded-full flex items-center justify-center" id="bestAqiIcon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-2xl font-bold" id="bestLocation">--</p>
                        <p class="text-sm text-slate-400" id="bestLocationAqi">AQI: --</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="dashboard-card p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold">Live AQI Map</h3>
                        <div class="data-pill">
                            <i class="fas fa-map-marker-alt"></i>
                            <span id="mapLocationsCount">0 locations</span>
                        </div>
                    </div>
                    <div id="map" class="h-96"></div>
                </div>

                <div class="dashboard-card p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold">Recent Readings</h3>
                        <div class="data-pill" id="lastUpdated">
                            <i class="fas fa-sync-alt"></i>
                            <span>Just now</span>
                        </div>
                    </div>
                    <div id="locationCards" class="space-y-4 max-h-96 overflow-y-auto pr-2">
                        <div class="animate-pulse flex space-x-4">
                            <div class="flex-1 space-y-4 py-1">
                                <div class="h-4 bg-slate-700 rounded w-3/4"></div>
                                <div class="space-y-2">
                                    <div class="h-4 bg-slate-700 rounded"></div>
                                    <div class="h-4 bg-slate-700 rounded w-5/6"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dashboard-card p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold">AQI Trends</h3>
                    <div class="flex items-center space-x-2">
                        <button class="data-pill active" id="trend24h">
                            <i class="fas fa-clock"></i>
                            <span>24h</span>
                        </button>
                        <button class="data-pill" id="trend30d">
                            <i class="fas fa-calendar-alt"></i>
                            <span>30d</span>
                        </button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="dashboard-card p-6">
                    <h3 class="font-semibold mb-4">Pollution Distribution</h3>
                    <div class="chart-container">
                        <canvas id="distributionChart"></canvas>
                    </div>
                </div>

                <div class="dashboard-card p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold">Notifications</h3>
                        @auth
                        <button id="createNotification"
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg font-medium hover:bg-blue-600 transition">
                            New Alert
                        </button>
                        @endauth
                    </div>
                    <div id="notificationsPanel" class="space-y-4 max-h-80 overflow-y-auto pr-2">
                        <div class="animate-pulse flex space-x-4">
                            <div class="flex-1 space-y-4 py-1">
                                <div class="h-4 bg-slate-700 rounded w-3/4"></div>
                                <div class="space-y-2">
                                    <div class="h-4 bg-slate-700 rounded"></div>
                                    <div class="h-4 bg-slate-700 rounded w-5/6"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @auth
            <div class="dashboard-card p-6">
                <h3 class="font-semibold mb-4">User Activity</h3>
                <div id="usersPanel" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="animate-pulse">
                        <div class="h-20 bg-slate-700 rounded-lg"></div>
                    </div>
                </div>
            </div>
            @endauth
        </div>
    </div>

    <!-- Notification Modal -->
    <div id="notificationModal" class="fixed inset-0 z-50 flex items-center justify-center modal-overlay hidden">
        <div class="modal-content bg-slate-800 rounded-2xl p-6 w-full max-w-md mx-4 border border-slate-700">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-white">Create Air Quality Alert</h3>
                <button id="closeModal" class="text-slate-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="notificationForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Alert Title</label>
                    <input type="text" id="notificationTitle"
                        class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Message</label>
                    <textarea id="notificationMessage" rows="3"
                        class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Location</label>
                    <select id="notificationLocation"
                        class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white">
                        <option value="">Select location</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancelModal"
                        class="px-4 py-2 text-slate-300 hover:bg-slate-700 rounded-lg transition">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">Send
                        Alert</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Admin Modals -->
    @if(Auth::check() && Auth::user()->is_admin)
    <!-- Manage Users Modal -->
    <div id="manageUsersModal" class="fixed inset-0 z-50 flex items-center justify-center modal-overlay hidden">
        <div class="modal-content bg-slate-800 rounded-2xl p-6 w-full max-w-4xl mx-4 border border-slate-700">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-white">Manage Users</h3>
                <button id="closeUsersModal" class="text-slate-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="overflow-y-auto max-h-[70vh]">
                <table class="min-w-full divide-y divide-slate-700">
                    <thead class="bg-slate-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody" class="bg-slate-800 divide-y divide-slate-700">
                        <!-- Users will be loaded here -->
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex justify-end">
                <button id="closeUsersModalBtn"
                    class="px-4 py-2 text-slate-300 hover:bg-slate-700 rounded-lg transition">Close</button>
            </div>
        </div>
    </div>

    <!-- System Settings Modal -->
    <div id="systemSettingsModal" class="fixed inset-0 z-50 flex items-center justify-center modal-overlay hidden">
        <div class="modal-content bg-slate-800 rounded-2xl p-6 w-full max-w-2xl mx-4 border border-slate-700">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-white">System Settings</h3>
                <button id="closeSettingsModal" class="text-slate-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="settingsForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Data Refresh Interval (minutes)</label>
                    <input type="number" id="refreshInterval" min="1" max="60"
                        class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">API Key</label>
                    <input type="text" id="apiKey"
                        class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="maintenanceMode"
                        class="w-4 h-4 text-blue-600 bg-slate-700 border-slate-600 rounded focus:ring-blue-500">
                    <label for="maintenanceMode" class="ms-2 text-sm font-medium text-slate-300">Maintenance
                        Mode</label>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" id="closeSettingsModalBtn"
                        class="px-4 py-2 text-slate-300 hover:bg-slate-700 rounded-lg transition">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">Save
                        Settings</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <script>
        const isAuthenticated = {!! auth()->check() ? 'true' : 'false' !!};
        const isAdmin = {!! auth()->check() && auth()->user()->is_admin ? 'true' : 'false' !!};
        let map;
        let markers = [];
        let charts = {};
        let useMockData = false;
        let currentData = [];
        let currentTimeRange = '24h';

        function updateTime() {
            const now = new Date();
            document.getElementById('currentTime').textContent = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }

        function getAQIColor(aqi) {
            if (aqi <= 50) return { bg: '#10b981', text: 'Good', icon: 'fa-smile' };
            if (aqi <= 100) return { bg: '#f59e0b', text: 'Moderate', icon: 'fa-meh' };
            if (aqi <= 150) return { bg: '#f97316', text: 'Unhealthy for Sensitive Groups', icon: 'fa-frown' };
            if (aqi <= 200) return { bg: '#ef4444', text: 'Unhealthy', icon: 'fa-sad-tear' };
            if (aqi <= 300) return { bg: '#b91c1c', text: 'Very Unhealthy', icon: 'fa-hospital' };
            return { bg: '#6b21a8', text: 'Hazardous', icon: 'fa-skull' };
        }

        function generateMockData() {
            const locationCoords = {
                'Colombo': { lat: 6.9271, lng: 79.8612 },
                'Rajagiriya': { lat: 6.9090, lng: 79.8960 },
                'Kirulapana': { lat: 6.8782, lng: 79.8764 },
                'Maradana': { lat: 6.9287, lng: 79.8639 }
            };

            const mockData = [];
            const now = new Date();

            const intervals = currentTimeRange === '24h' ? 24 : 30;
            const timeStep = currentTimeRange === '24h' ? 3600000 : 86400000;

            for (let i = 0; i < intervals; i++) {
                Object.entries(locationCoords).forEach(([location, coords]) => {
                    const time = new Date(now - i * timeStep);
                    const baseAQI = Math.floor(Math.random() * 100) + 50;
                    const variation = currentTimeRange === '30d' ? Math.sin(i / 3) * 20 : 0;

                    mockData.push({
                        location: location,
                        aqi: Math.max(0, Math.min(500, Math.floor(baseAQI + variation))),
                        reading_time: time.toISOString(),
                        latitude: coords.lat,
                        longitude: coords.lng,
                        station: 'Simulated Station'
                    });
                });
            }

            return mockData;
        }

        function getLatestLocations(data) {
            const locations = [...new Set(data.map(item => item.location))];
            return locations.map(location => {
                const locationData = data.filter(d => d.location === location)
                    .sort((a, b) => new Date(b.reading_time) - new Date(a.reading_time));
                return locationData[0];
            });
        }

        function initMap(locations) {
            const center = { lat: 6.9271, lng: 79.8612 };

            if (!map) {
                map = new google.maps.Map(document.getElementById("map"), {
                    zoom: 11,
                    center: center,
                    mapTypeControl: true,
                    streetViewControl: false,
                    styles: [
                        {
                            featureType: "poi",
                            elementType: "labels",
                            stylers: [{ visibility: "off" }]
                        },
                        {
                            featureType: "water",
                            elementType: "geometry",
                            stylers: [{ color: "#0e1a2f" }]
                        },
                        {
                            featureType: "landscape",
                            elementType: "geometry",
                            stylers: [{ color: "#0f172a" }]
                        },
                        {
                            elementType: "labels.text.stroke",
                            stylers: [{ visibility: "off" }]
                        },
                        {
                            elementType: "labels.text.fill",
                            stylers: [{ color: "#94a3b8" }]
                        }
                    ]
                });
            }

            markers.forEach(marker => marker.setMap(null));
            markers = [];

            locations.forEach(location => {
                const position = {
                    lat: parseFloat(location.latitude),
                    lng: parseFloat(location.longitude)
                };

                const aqiInfo = getAQIColor(location.aqi);
                const marker = new google.maps.Marker({
                    position: position,
                    map: map,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        fillColor: aqiInfo.bg,
                        fillOpacity: 1,
                        strokeColor: '#ffffff',
                        strokeWeight: 2,
                        scale: 10
                    },
                    title: `${location.location} - AQI: ${location.aqi}`
                });

                const infoWindow = new google.maps.InfoWindow({
                    content: `
                        <div class="p-2 bg-slate-800 text-white rounded-lg">
                            <h3 class="font-bold">${location.location}</h3>
                            <div class="flex items-center mt-1">
                                <div class="w-4 h-4 rounded-full mr-2" style="background-color: ${aqiInfo.bg}"></div>
                                <span>AQI: ${location.aqi} (${aqiInfo.text})</span>
                            </div>
                            <p class="text-sm text-slate-400 mt-1">${location.station}</p>
                        </div>
                    `
                });

                marker.addListener('click', () => {
                    infoWindow.open(map, marker);
                });

                markers.push(marker);
            });

            document.getElementById('mapLocationsCount').textContent = `${locations.length} locations`;
        }

        function createMiniTrendChart(data) {
            const ctx = document.getElementById('miniTrendChart').getContext('2d');
            const filteredData = data.filter(d => d.location === 'Colombo')
                .sort((a, b) => new Date(a.reading_time) - new Date(b.reading_time))
                .slice(0, 12);

            const labels = filteredData.map(d =>
                new Date(d.reading_time).toLocaleTimeString([], { hour: '2-digit' }));

            if (charts.miniTrendChart) charts.miniTrendChart.destroy();

            charts.miniTrendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels.reverse(),
                    datasets: [{
                        data: filteredData.map(d => d.aqi).reverse(),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { display: false },
                        x: { display: false }
                    }
                }
            });
        }

        function createTrendChart(data) {
            const ctx = document.getElementById('trendChart').getContext('2d');
            const locations = [...new Set(data.map(item => item.location))];
            const timeLabels = [...new Set(data.map(item =>
                currentTimeRange === '24h'
                    ? new Date(item.reading_time).toLocaleTimeString([], { hour: '2-digit' })
                    : new Date(item.reading_time).toLocaleDateString([], { day: 'numeric', month: 'short' }))
            )].sort();

            const datasets = locations.map(location => {
                const locationData = data.filter(d => d.location === location);
                const aqiInfo = getAQIColor(locationData[0].aqi);
                return {
                    label: location,
                    data: locationData.map(d => d.aqi),
                    borderColor: aqiInfo.bg,
                    backgroundColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 2,
                    tension: currentTimeRange === '30d' ? 0.4 : 0,
                    fill: true
                };
            });

            if (charts.trendChart) {
                charts.trendChart.destroy();
            }

            charts.trendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: timeLabels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: '#e2e8f0'
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: '#0f172a',
                            titleColor: '#e2e8f0',
                            bodyColor: '#e2e8f0',
                            borderColor: '#1e293b',
                            borderWidth: 1
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            grid: {
                                color: 'rgba(255, 255, 255, 0.05)'
                            },
                            ticks: {
                                color: '#94a3b8'
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(255, 255, 255, 0.05)'
                            },
                            ticks: {
                                color: '#94a3b8'
                            }
                        }
                    }
                }
            });
        }

        function createDistributionChart(data) {
            const ctx = document.getElementById('distributionChart').getContext('2d');
            const aqiLevels = {
                'Good (0-50)': 0,
                'Moderate (51-100)': 0,
                'Unhealthy for Sensitive Groups (101-150)': 0,
                'Unhealthy (151-200)': 0,
                'Very Unhealthy (201-300)': 0,
                'Hazardous (301+)': 0
            };

            data.forEach(reading => {
                if (reading.aqi <= 50) aqiLevels['Good (0-50)']++;
                else if (reading.aqi <= 100) aqiLevels['Moderate (51-100)']++;
                else if (reading.aqi <= 150) aqiLevels['Unhealthy for Sensitive Groups (101-150)']++;
                else if (reading.aqi <= 200) aqiLevels['Unhealthy (151-200)']++;
                else if (reading.aqi <= 300) aqiLevels['Very Unhealthy (201-300)']++;
                else aqiLevels['Hazardous (301+)']++;
            });

            if (charts.distributionChart) {
                charts.distributionChart.destroy();
            }

            charts.distributionChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(aqiLevels),
                    datasets: [{
                        data: Object.values(aqiLevels),
                        backgroundColor: ['#10b981', '#f59e0b', '#f97316', '#ef4444', '#b91c1c', '#6b21a8'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                color: '#e2e8f0'
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        }

        function updateDashboard(data) {
            const latestLocations = getLatestLocations(data);
            const aqiValues = latestLocations.map(loc => loc.aqi);
            const avgAqi = Math.round(aqiValues.reduce((a, b) => a + b, 0) / aqiValues.length);
            const worstLocation = latestLocations.reduce((worst, current) => current.aqi > worst.aqi ? current : worst);
            const bestLocation = latestLocations.reduce((best, current) => current.aqi < best.aqi ? current : best);

            const avgAqiInfo = getAQIColor(avgAqi);
            const worstAqiInfo = getAQIColor(worstLocation.aqi);
            const bestAqiInfo = getAQIColor(bestLocation.aqi);

            $('#currentAqi').text(avgAqi);
            $('#aqiStatus').text(avgAqiInfo.text);
            $('#aqiStatusIcon').css('background-color', `${avgAqiInfo.bg}20`).html(`<i class="fas ${avgAqiInfo.icon} text-xl" style="color: ${avgAqiInfo.bg}"></i>`);

            $('#worstAqiIcon').css('background-color', `${worstAqiInfo.bg}20`).html(`<i class="fas ${worstAqiInfo.icon} text-xl" style="color: ${worstAqiInfo.bg}"></i>`);
            $('#worstLocation').text(worstLocation.location);
            $('#worstLocationAqi').text(`AQI: ${worstLocation.aqi} (${worstAqiInfo.text})`);

            $('#bestAqiIcon').css('background-color', `${bestAqiInfo.bg}20`).html(`<i class="fas ${bestAqiInfo.icon} text-xl" style="color: ${bestAqiInfo.bg}"></i>`);
            $('#bestLocation').text(bestLocation.location);
            $('#bestLocationAqi').text(`AQI: ${bestLocation.aqi} (${bestAqiInfo.text})`);

            $('#lastUpdated span').text(new Date().toLocaleTimeString());

            let locationCardsHtml = '';
            latestLocations.forEach(location => {
                const aqiInfo = getAQIColor(location.aqi);
                locationCardsHtml += `
                    <div class="bg-slate-800 rounded-xl p-4 transition hover:shadow-lg">
                        <div class="flex justify-between items-center">
                            <h4 class="font-semibold">${location.location}</h4>
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full mr-2" style="background-color: ${aqiInfo.bg}"></div>
                                <span class="font-medium">${location.aqi}</span>
                            </div>
                        </div>
                        <div class="mt-2 flex justify-between items-center text-sm">
                            <span class="text-slate-400">${aqiInfo.text}</span>
                            <span class="text-slate-500">${location.station}</span>
                        </div>
                    </div>
                `;
            });
            $('#locationCards').html(locationCardsHtml);

            initMap(latestLocations);
            createMiniTrendChart(data);
            createTrendChart(data);
            createDistributionChart(data);
            updateLocationDropdown(latestLocations);

            gsap.from("#currentAqi, #aqiStatus, #aqiStatusIcon, #worstLocation, #bestLocation, .chart-container", {
                opacity: 0,
                y: 10,
                duration: 0.5,
                stagger: 0.1
            });
        }

        function updateNotifications() {
            $.get('/notifications', function(notifications) {
                let html = '';
                notifications.forEach(notification => {
                    const date = new Date(notification.created_at).toLocaleString();
                    const aqiInfo = getAQIColor(notification.aqi_level);
                    html += `
                        <div class="notification-card bg-slate-800 rounded-xl p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-semibold">${notification.title}</h4>
                                    <p class="text-sm text-slate-400 mt-1">${notification.user.name} • ${date}</p>
                                </div>
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-medium" style="background-color: ${aqiInfo.bg}">
                                    ${notification.aqi_level}
                                </div>
                            </div>
                            <p class="mt-2 text-slate-300">${notification.message}</p>
                            <p class="text-sm text-slate-500 mt-2">Location: ${notification.location}</p>
                        </div>
                    `;
                });
                $('#notificationsPanel').html(html || '<p class="text-slate-400 text-center py-4">No notifications yet</p>');
            });
        }

        function updateUsers() {
            $.get('/users', function(users) {
                let html = '';
                users.forEach(user => {
                    html += `
                        <div class="bg-slate-800 rounded-xl p-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-blue-500/10 flex items-center justify-center">
                                    <span class="text-blue-400 font-medium">${user.name.charAt(0)}</span>
                                </div>
                                <div>
                                    <h4 class="font-semibold">${user.name}</h4>
                                    <p class="text-sm text-slate-400">${user.email}</p>
                                </div>
                                ${user.is_admin ? '<span class="ml-auto admin-badge text-xs font-medium px-2.5 py-0.5 rounded-full">Admin</span>' : ''}
                            </div>
                        </div>
                    `;
                });
                $('#usersPanel').html(html);
            });
        }

        function updateLocationDropdown(data) {
            const locations = [...new Set(data.map(item => item.location))];
            let html = '<option value="">Select location</option>';
            locations.forEach(location => {
                html += `<option value="${location}">${location}</option>`;
            });
            $('#notificationLocation').html(html);
        }

        function updateData() {
            let data;

            if (useMockData) {
                data = generateMockData();
                currentData = data;
                updateDashboard(data);
            } else {
                $.get('/test-apis', function(apiData) {
                    const normalizedData = apiData.map(item => ({
                        location: item.location,
                        aqi: item.air_quality_data.data.aqi,
                        reading_time: new Date().toISOString(),
                        latitude: item.latitude,
                        longitude: item.longitude,
                        station: item.air_quality_data.data.city.name
                    }));
                    currentData = normalizedData;
                    updateDashboard(normalizedData);
                });

                $.get('/get-readings', function(readingsData) {
                    const allReadings = Object.values(readingsData).flat();
                    createTrendChart(allReadings);
                    createDistributionChart(allReadings);
                });
            }
        }

        // Modal handling functions
        function showModal(modalId) {
            $(`#${modalId}`).removeClass('hidden').addClass('flex');
            document.body.style.overflow = 'hidden';
            gsap.to(`#${modalId} .modal-content`, {
                opacity: 1,
                y: 0,
                duration: 0.3,
                ease: 'power2.out'
            });
        }

        function hideModal(modalId) {
            gsap.to(`#${modalId} .modal-content`, {
                opacity: 0,
                y: 20,
                duration: 0.2,
                ease: 'power2.in',
                onComplete: () => {
                    $(`#${modalId}`).removeClass('flex').addClass('hidden');
                    document.body.style.overflow = '';
                }
            });
        }

        // Admin functions
        function loadUsersForManagement() {
            if (!isAdmin) return;

            $.get('/users', function(users) {
                let html = '';
                users.forEach(user => {
                    html += `
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">${user.name}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">${user.email}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                                ${user.is_admin ?
                                    '<span class="admin-badge px-2 py-1 rounded-full text-xs">Admin</span>' :
                                    '<span class="bg-green-500/10 text-green-500 px-2 py-1 rounded-full text-xs">Active</span>'}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                                <div class="flex space-x-2">
                                    <button class="edit-user px-2 py-1 bg-blue-500/10 text-blue-400 rounded text-xs hover:bg-blue-500/20" data-id="${user.id}">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </button>
                                    <button class="delete-user px-2 py-1 bg-red-500/10 text-red-400 rounded text-xs hover:bg-red-500/20" data-id="${user.id}">
                                        <i class="fas fa-trash-alt mr-1"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
                $('#usersTableBody').html(html);
            });
        }

        function loadSystemSettings() {
            if (!isAdmin) return;

            $.get('/system-settings', function(settings) {
                $('#refreshInterval').val(settings.refresh_interval || 5);
                $('#apiKey').val(settings.api_key || '');
                $('#maintenanceMode').prop('checked', settings.maintenance_mode || false);
            });
        }

        $(document).ready(function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            updateTime();
            setInterval(updateTime, 60000);

            updateData();
            setInterval(updateData, 300000);

            updateNotifications();
            if (isAuthenticated) {
                updateUsers();
            }
            setInterval(updateNotifications, 300000);

            // Data toggle handler
            $('#dataToggle').change(function() {
                useMockData = $(this).is(':checked');
                updateData();

                gsap.to(".dashboard-card", {
                    y: 10,
                    opacity: 0.5,
                    duration: 0.3,
                    onComplete: () => {
                        gsap.to(".dashboard-card", {
                            y: 0,
                            opacity: 1,
                            duration: 0.3
                        });
                    }
                });
            });

            // Trend time range handler
            $('[id^="trend"]').click(function() {
                currentTimeRange = this.id.replace('trend', '').toLowerCase();
                $('[id^="trend"]').removeClass('active');
                $(this).addClass('active');
                updateData();
            });

            $('#trend24h').addClass('active');

            // Notification modal handling
            $('#createNotification').click(function() {
                showModal('notificationModal');
            });

            $('#closeModal, #cancelModal').click(function() {
                hideModal('notificationModal');
            });

            // Close modal when clicking outside
            $('#notificationModal').click(function(e) {
                if (e.target === this) {
                    hideModal('notificationModal');
                }
            });

            // Notification form submission
            $('#notificationForm').submit(function(e) {
                e.preventDefault();
                const location = $('#notificationLocation').val();
                const title = $('#notificationTitle').val();
                const message = $('#notificationMessage').val();

                if (!location || !title || !message) {
                    alert('Please fill in all fields');
                    return;
                }

                const locationData = currentData.find(d => d.location === location);
                const aqi = locationData?.aqi || 0; // Default to 0 if not found

                $.ajax({
                    url: '/notifications',
                    method: 'POST',
                    data: {
                        title: title,
                        message: message,
                        location: location,
                        aqi_level: aqi
                    },
                    success: function() {
                        hideModal('notificationModal');
                        $('#notificationForm')[0].reset();
                        updateNotifications();

                        // Show success message
                        const successMsg = $(`
                            <div class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg">
                                Notification created successfully!
                            </div>
                        `);
                        $('body').append(successMsg);
                        setTimeout(() => successMsg.remove(), 3000);
                    },
                    error: function(xhr) {
                        alert('Error creating notification: ' + (xhr.responseJSON?.message || 'An error occurred'));
                    }
                });
            });

            // Admin functions
            if (isAdmin) {
                // Refresh data button
                $('#refreshData').click(function() {
                    $(this).html('<i class="fas fa-spinner fa-spin mr-2"></i> Refreshing...');
                    $.get('/force-refresh', function() {
                        updateData();
                        $('#refreshData').html('<i class="fas fa-sync-alt mr-2"></i> Force Data Refresh');

                        // Show success message
                        const successMsg = $(`
                            <div class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg">
                                Data refreshed successfully!
                            </div>
                        `);
                        $('body').append(successMsg);
                        setTimeout(() => successMsg.remove(), 3000);
                    });
                });

                // Manage users modal
                $('#manageUsers').click(function() {
                    loadUsersForManagement();
                    showModal('manageUsersModal');
                });

                $('#closeUsersModal, #closeUsersModalBtn').click(function() {
                    hideModal('manageUsersModal');
                });

                // System settings modal
                $('#systemSettings').click(function() {
                    loadSystemSettings();
                    showModal('systemSettingsModal');
                });

                $('#closeSettingsModal, #closeSettingsModalBtn').click(function() {
                    hideModal('systemSettingsModal');
                });

                // Settings form submission
                $('#settingsForm').submit(function(e) {
                    e.preventDefault();
                    const settings = {
                        refresh_interval: $('#refreshInterval').val(),
                        api_key: $('#apiKey').val(),
                        maintenance_mode: $('#maintenanceMode').is(':checked')
                    };

                    $.ajax({
                        url: '/system-settings',
                        method: 'POST',
                        data: settings,
                        success: function() {
                            hideModal('systemSettingsModal');

                            // Show success message
                            const successMsg = $(`
                                <div class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg">
                                    Settings saved successfully!
                                </div>
                            `);
                            $('body').append(successMsg);
                            setTimeout(() => successMsg.remove(), 3000);
                        },
                        error: function(xhr) {
                            alert('Error saving settings: ' + (xhr.responseJSON?.message || 'An error occurred'));
                        }
                    });
                });

                // User management actions
                $(document).on('click', '.edit-user', function() {
                    const userId = $(this).data('id');
                    // Implement edit user functionality
                    alert('Edit user with ID: ' + userId);
                });

                $(document).on('click', '.delete-user', function() {
                    const userId = $(this).data('id');
                    if (confirm('Are you sure you want to delete this user?')) {
                        $.ajax({
                            url: `/users/${userId}`,
                            method: 'DELETE',
                            success: function() {
                                loadUsersForManagement();
                                updateUsers();

                                // Show success message
                                const successMsg = $(`
                                    <div class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg">
                                        User deleted successfully!
                                    </div>
                                `);
                                $('body').append(successMsg);
                                setTimeout(() => successMsg.remove(), 3000);
                            },
                            error: function(xhr) {
                                alert('Error deleting user: ' + (xhr.responseJSON?.message || 'An error occurred'));
                            }
                        });
                    }
                });
            }
        });
    </script>
</body>

</html>
