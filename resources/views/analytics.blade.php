<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AirViz | Air Quality Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
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
                <a href="/" class="nav-link">Dashboard</a>
                <a href="/analytics" class="nav-link active-nav font-medium">Analytics</a>
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
                    <span
                        class="bg-blue-500/10 text-blue-400 text-xs font-medium px-2.5 py-0.5 rounded-full">Admin</span>
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
                    <h1 class="text-3xl font-bold">Air Quality Analytics</h1>
                    <p class="text-slate-400">Real-time and historical air quality metrics</p>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="data-pill">
                        <i class="fas fa-clock"></i>
                        <span id="currentTime">Loading...</span>
                    </div>
                    <div class="data-pill">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Colombo</span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm">Live Data</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="dataToggle" class="sensor-toggle">
                        </label>
                        <span class="text-sm">Simulation</span>
                    </div>
                </div>
            </div>

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
                        <h3 class="font-semibold">Pollutant Levels</h3>
                        <div class="flex space-x-2">
                            <button class="data-pill active">24h</button>
                            <button class="data-pill">7d</button>
                            <button class="data-pill">30d</button>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span>PM2.5</span>
                                <span id="pm25Value">-- µg/m³</span>
                            </div>
                            <div class="w-full bg-slate-700 rounded-full h-2">
                                <div class="bg-amber-400 h-2 rounded-full" id="pm25Bar" style="width: 0%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span>PM10</span>
                                <span id="pm10Value">-- µg/m³</span>
                            </div>
                            <div class="w-full bg-slate-700 rounded-full h-2">
                                <div class="bg-purple-400 h-2 rounded-full" id="pm10Bar" style="width: 0%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span>NO₂</span>
                                <span id="no2Value">-- ppm</span>
                            </div>
                            <div class="w-full bg-slate-700 rounded-full h-2">
                                <div class="bg-cyan-400 h-2 rounded-full" id="no2Bar" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-card p-6">
                    <h3 class="font-semibold mb-4">Health Recommendations</h3>
                    <div class="space-y-3" id="recommendations">
                        <div class="flex items-start space-x-3">
                            <div
                                class="w-6 h-6 rounded-full bg-blue-500/20 flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-mask-face text-blue-400 text-xs"></i>
                            </div>
                            <p class="text-sm">Loading health recommendations...</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="dashboard-card p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold">AQI Trends</h3>
                        <div class="flex items-center space-x-2">
                            <button class="p-1 rounded hover:bg-slate-700/50" id="trend24h">
                                <i class="fas fa-clock text-sm"></i>
                            </button>
                            <button class="p-1 rounded hover:bg-slate-700/50" id="trend30d">
                                <i class="fas fa-calendar-alt text-sm"></i>
                            </button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>

                <div class="dashboard-card p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold">Pollutant Composition</h3>
                        <div class="flex items-center space-x-2">
                            <button class="p-1 rounded hover:bg-slate-700/50" id="composition24h">
                                <i class="fas fa-clock text-sm"></i>
                            </button>
                            <button class="p-1 rounded hover:bg-slate-700/50" id="composition30d">
                                <i class="fas fa-calendar-alt text-sm"></i>
                            </button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="compositionChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="dashboard-card p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold">Location Comparison</h3>
                    <div class="flex items-center space-x-2">
                        <button class="p-1 rounded hover:bg-slate-700/50" id="compare24h">
                            <i class="fas fa-clock text-sm"></i>
                        </button>
                        <button class="p-1 rounded hover:bg-slate-700/50" id="compare30d">
                            <i class="fas fa-calendar-alt text-sm"></i>
                        </button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="comparisonChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        let charts = {};
        let useMockData = false;
        let currentTimeRange = '24h';

        function updateTime() {
            const now = new Date();
            document.getElementById('currentTime').textContent = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }

        function getAQIColor(aqi) {
            if (aqi <= 50) return { bg: '#4CAF50', text: 'Good', icon: 'fa-smile', rec: 'Air quality is satisfactory.' };
            if (aqi <= 100) return { bg: '#FDD835', text: 'Moderate', icon: 'fa-meh', rec: 'Unusually sensitive people should consider reducing prolonged outdoor exertion.' };
            if (aqi <= 150) return { bg: '#FF9800', text: 'Unhealthy for Sensitive Groups', icon: 'fa-frown', rec: 'People with heart or lung disease should reduce prolonged outdoor exertion.' };
            if (aqi <= 200) return { bg: '#F44336', text: 'Unhealthy', icon: 'fa-sad-tear', rec: 'Everyone may begin to experience health effects.' };
            if (aqi <= 300) return { bg: '#8B0000', text: 'Very Unhealthy', icon: 'fa-hospital', rec: 'Health alert: everyone may experience more serious health effects.' };
            return { bg: '#800080', text: 'Hazardous', icon: 'fa-skull', rec: 'Health warnings of emergency conditions.' };
        }

        function generateMockData() {
            const locations = ['Colombo', 'Rajagiriya', 'Kirulapana', 'Maradana'];
            const mockData = [];
            const now = new Date();

            const intervals = currentTimeRange === '24h' ? 24 : 30;
            const timeStep = currentTimeRange === '24h' ? 3600000 : 86400000;

            for (let i = 0; i < intervals; i++) {
                locations.forEach(location => {
                    const time = new Date(now - i * timeStep);
                    const baseAQI = Math.floor(Math.random() * 100) + 50;
                    const variation = currentTimeRange === '30d' ? Math.sin(i / 3) * 20 : 0;

                    mockData.push({
                        location: location,
                        aqi: Math.max(0, Math.min(500, Math.floor(baseAQI + variation))),
                        reading_time: time.toISOString(),
                        co: (Math.random() * 4 + 1).toFixed(2),
                        no2: (Math.random() * 0.1 + 0.01).toFixed(2),
                        o3: (Math.random() * 0.07 + 0.02).toFixed(2),
                        so2: (Math.random() * 0.1 + 0.01).toFixed(2),
                        pm10: (Math.random() * 100 + 20).toFixed(2),
                        pm25: (Math.random() * 50 + 10).toFixed(2)
                    });
                });
            }

            return mockData;
        }

        function updateCurrentMetrics(data) {
            const latest = data[0];
            const aqiInfo = getAQIColor(latest.aqi);

            document.getElementById('currentAqi').textContent = latest.aqi;
            document.getElementById('aqiStatus').textContent = aqiInfo.text;

            const iconEl = document.getElementById('aqiStatusIcon');
            iconEl.style.backgroundColor = `${aqiInfo.bg}20`;
            iconEl.innerHTML = `<i class="fas ${aqiInfo.icon} text-xl" style="color: ${aqiInfo.bg}"></i>`;

            document.getElementById('pm25Value').textContent = `${latest.pm25} µg/m³`;
            document.getElementById('pm10Value').textContent = `${latest.pm10} µg/m³`;
            document.getElementById('no2Value').textContent = `${latest.no2} ppm`;

            document.getElementById('pm25Bar').style.width = `${Math.min(100, latest.pm25 / 2)}%`;
            document.getElementById('pm10Bar').style.width = `${Math.min(100, latest.pm10 / 2)}%`;
            document.getElementById('no2Bar').style.width = `${Math.min(100, latest.no2 * 1000)}%`;

            const recContainer = document.getElementById('recommendations');
            recContainer.innerHTML = `
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 rounded-full bg-blue-500/20 flex items-center justify-center flex-shrink-0 mt-1">
                        <i class="fas ${aqiInfo.icon} text-blue-400 text-xs"></i>
                    </div>
                    <p class="text-sm">${aqiInfo.rec}</p>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 rounded-full bg-blue-500/20 flex items-center justify-center flex-shrink-0 mt-1">
                        <i class="fas fa-tree text-blue-400 text-xs"></i>
                    </div>
                    <p class="text-sm">Consider indoor plants to improve air quality.</p>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 rounded-full bg-blue-500/20 flex items-center justify-center flex-shrink-0 mt-1">
                        <i class="fas fa-window-maximize text-blue-400 text-xs"></i>
                    </div>
                    <p class="text-sm">${latest.aqi > 100 ? 'Keep windows closed' : 'Open windows for ventilation'}.</p>
                </div>
            `;

            gsap.from("#currentAqi, #aqiStatus, #aqiStatusIcon, #pm25Value, #pm10Value, #no2Value, #recommendations div", {
                opacity: 0,
                y: 10,
                duration: 0.5,
                stagger: 0.1
            });
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
            const colomboData = data.filter(d => d.location === 'Colombo')
                .sort((a, b) => new Date(a.reading_time) - new Date(b.reading_time));

            const labels = colomboData.map(d =>
                currentTimeRange === '24h'
                    ? new Date(d.reading_time).toLocaleTimeString([], { hour: '2-digit' })
                    : new Date(d.reading_time).toLocaleDateString([], { day: 'numeric', month: 'short' }));

            if (charts.trendChart) charts.trendChart.destroy();

            charts.trendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'AQI',
                        data: colomboData.map(d => d.aqi),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        tension: currentTimeRange === '30d' ? 0.4 : 0,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => `AQI: ${ctx.raw} (${getAQIColor(ctx.raw).text})`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            grid: { color: 'rgba(255, 255, 255, 0.05)' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        function createCompositionChart(data) {
            const ctx = document.getElementById('compositionChart').getContext('2d');
            const latest = data[0];

            if (charts.compositionChart) charts.compositionChart.destroy();

            charts.compositionChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['PM2.5', 'PM10', 'NO₂', 'CO', 'O₃', 'SO₂'],
                    datasets: [{
                        data: [
                            latest.pm25,
                            latest.pm10,
                            latest.no2 * 100,
                            latest.co,
                            latest.o3 * 10,
                            latest.so2 * 10
                        ],
                        backgroundColor: [
                            '#F59E0B',
                            '#8B5CF6',
                            '#06B6D4',
                            '#EF4444',
                            '#10B981',
                            '#F97316'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 12,
                                padding: 16,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => {
                                    let value = ctx.raw;
                                    let unit = ctx.label === 'PM2.5' || ctx.label === 'PM10'
                                        ? 'µg/m³'
                                        : ctx.label === 'NO₂' || ctx.label === 'CO'
                                            ? 'ppm'
                                            : 'ppb';
                                    return `${ctx.label}: ${value.toFixed(2)} ${unit}`;
                                }
                            }
                        }
                    }
                }
            });
        }

        function createComparisonChart(data) {
            const ctx = document.getElementById('comparisonChart').getContext('2d');
            const locations = [...new Set(data.map(d => d.location))];

            const avgAqi = locations.map(location => {
                const locData = data.filter(d => d.location === location);
                return {
                    location: location,
                    aqi: locData.reduce((sum, curr) => sum + curr.aqi, 0) / locData.length
                };
            });

            if (charts.comparisonChart) charts.comparisonChart.destroy();

            charts.comparisonChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: avgAqi.map(d => d.location),
                    datasets: [{
                        label: 'Average AQI',
                        data: avgAqi.map(d => d.aqi),
                        backgroundColor: avgAqi.map(d => getAQIColor(d.aqi).bg),
                        borderWidth: 0,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: ctx => `Avg AQI: ${ctx.raw.toFixed(1)} (${getAQIColor(ctx.raw).text})`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(255, 255, 255, 0.05)' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        function updateCharts() {
            let data = useMockData ? generateMockData() : [];

            if (!useMockData) {
                fetch('/get-readings')
                    .then(response => response.json())
                    .then(jsonData => {
                        data = Object.values(jsonData).flat();
                        renderCharts(data);
                    })
                    .catch(error => {
                        console.error('Error fetching data:', error);
                        useMockData = true;
                        updateCharts();
                    });
            } else {
                renderCharts(data);
            }
        }

        function renderCharts(data) {
            updateCurrentMetrics(data);
            createMiniTrendChart(data);
            createTrendChart(data);
            createCompositionChart(data);
            createComparisonChart(data);

            gsap.from(".chart-container", {
                opacity: 0,
                y: 20,
                duration: 0.6,
                stagger: 0.1
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateTime();
            setInterval(updateTime, 60000);

            updateCharts();
            setInterval(updateCharts, 300000);

            document.getElementById('dataToggle').addEventListener('change', function() {
                useMockData = this.checked;
                document.querySelectorAll('.data-pill').forEach(el => {
                    el.classList.toggle('active', el.textContent === currentTimeRange);
                });
                updateCharts();

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

            const timeRangeButtons = ['24h', '30d'].map(id => document.getElementById(`trend${id}`));
            timeRangeButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    currentTimeRange = this.id.replace('trend', '').toLowerCase();
                    timeRangeButtons.forEach(b => b.classList.toggle('bg-slate-700/50', b === this));
                    updateCharts();
                });
            });

            document.getElementById('trend24h').classList.add('bg-slate-700/50');
        });
    </script>
</body>

</html>
