@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-lg rounded-xl transition-colors">
            <div class="p-6 bg-white border-b border-gray-200 transition-colors">
                <h1 class="text-2xl font-bold mb-6 text-blue-600 transition-colors">Air Quality Dashboard - Colombo Metropolitan Area</h1>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Map and sensor information -->
                    <div class="md:col-span-2">
                        <div id="map" class="map-container rounded-xl shadow-lg overflow-hidden"></div>
                        
                        <div class="mt-6">
                            <h2 class="text-lg font-semibold mb-3">AQI Legend</h2>
                            <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
                                @foreach($alertThresholds as $threshold)
                                <div class="rounded-lg px-3 py-2 text-center shadow-sm" style="background-color: {{ $threshold->color_code }}; color: {{ in_array($threshold->category, ['Unhealthy', 'Very Unhealthy', 'Hazardous']) ? 'white' : 'black' }}">
                                    {{ $threshold->category }}
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <!-- Current weather and selected sensor info -->
                    <div>
                        <div class="bg-gray-100 p-5 rounded-xl shadow-md mb-6 transition-colors">
                            <h2 class="text-lg font-semibold mb-3 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M5.5 16a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 16h-8z" />
                                </svg>
                                Current Weather
                            </h2>
                            <div id="weather-info" class="transition-colors">
                                <p class="text-gray-500">Loading weather data...</p>
                            </div>
                        </div>
                        
                        <div class="bg-gray-100 p-5 rounded-xl shadow-md transition-colors">
                            <h2 class="text-lg font-semibold mb-3 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                                Selected Sensor
                            </h2>
                            <div id="selected-sensor" class="transition-colors">
                                <p class="text-gray-500">Click on a sensor in the map to view details.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Historical data chart -->
                <div class="mt-8">
                    <h2 class="text-lg font-semibold mb-3 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Historical AQI Data
                    </h2>
                    <div class="bg-gray-100 p-5 rounded-xl shadow-md transition-colors">
                        <div id="chart-container" style="height: 350px;">
                            <canvas id="aqi-chart"></canvas>
                        </div>
                        
                        <div class="mt-5 text-center" id="timeframe-selector">
                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg mx-2 shadow-sm transition-colors" data-timeframe="day">24 Hours</button>
                            <button class="bg-gray-300 hover:bg-blue-700 text-gray-800 hover:text-white font-bold py-2 px-4 rounded-lg mx-2 shadow-sm transition-colors" data-timeframe="week">Week</button>
                            <button class="bg-gray-300 hover:bg-blue-700 text-gray-800 hover:text-white font-bold py-2 px-4 rounded-lg mx-2 shadow-sm transition-colors" data-timeframe="month">Month</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Initialize the map
    const map = L.map('map').setView([6.9271, 79.8612], 12); // Colombo coordinates
    
    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Create a marker object to track the currently selected sensor
    let selectedMarker = null;
    let selectedSensor = null;
    let chart = null;
    let currentTimeframe = 'day';
    
    // Function to set AQI class based on value
    function getAqiClass(aqi) {
        if (aqi <= 50) return 'aqi-good';
        if (aqi <= 100) return 'aqi-moderate';
        if (aqi <= 150) return 'aqi-sensitive';
        if (aqi <= 200) return 'aqi-unhealthy';
        if (aqi <= 300) return 'aqi-very-unhealthy';
        return 'aqi-hazardous';
    }
    
    // Function to get marker color based on AQI
    function getMarkerColor(aqi) {
        if (aqi <= 50) return '#00E400';
        if (aqi <= 100) return '#FFFF00';
        if (aqi <= 150) return '#FF7E00';
        if (aqi <= 200) return '#FF0000';
        if (aqi <= 300) return '#99004C';
        return '#7E0023';
    }
    
    // Function to create circle marker
    function createMarker(sensor) {
        const aqi = sensor.latest_reading ? sensor.latest_reading.aqi : 0;
        const marker = L.circleMarker([sensor.latitude, sensor.longitude], {
            radius: 12, // Slightly larger markers
            fillColor: getMarkerColor(aqi),
            color: '#000',
            weight: 1,
            opacity: 1,
            fillOpacity: 0.8
        });
        
        // Add popup
        marker.bindPopup(`
            <div class="sensor-popup">
                <h3 class="font-bold">${sensor.name}</h3>
                <p>${sensor.location_name}</p>
                ${sensor.latest_reading ? `
                    <div class="mt-2 ${getAqiClass(aqi)} p-2 rounded-lg text-center">
                        <span class="font-bold">AQI: ${aqi.toFixed(0)}</span><br>
                        <span>${sensor.latest_reading.category}</span>
                    </div>
                    <p class="mt-1 text-xs">Last updated: ${new Date(sensor.latest_reading.recorded_at).toLocaleString()}</p>
                ` : '<p>No readings available</p>'}
                <button class="mt-2 bg-blue-500 hover:bg-blue-700 text-white text-xs py-1 px-2 rounded-lg w-full transition-colors">View Details</button>
            </div>
        `);
        
        // Add click event
        marker.on('click', function() {
            viewSensorDetails(sensor.id);
        });
        
        return marker;
    }
    
    // Load sensors
    let sensors = @json($sensors);
    
    // Add markers for each sensor
    sensors.forEach(function(sensor) {
        if (sensor.status === 'active') {
            createMarker(sensor).addTo(map);
        }
    });
    
    // Function to load and display weather data
    function loadWeatherData() {
        fetch('/weather')
            .then(response => response.json())
            .then(data => {
                if (data.name) {
                    const html = `
                        <div class="flex items-center bg-white bg-opacity-40 p-3 rounded-lg">
                            <img src="https://openweathermap.org/img/wn/${data.weather[0].icon}@2x.png" alt="${data.weather[0].description}" class="w-16 h-16">
                            <div>
                                <p class="font-bold text-lg">${data.name}</p>
                                <p class="text-xl">${Math.round(data.main.temp)}°C</p>
                                <p class="capitalize">${data.weather[0].description}</p>
                                <p class="text-sm mt-1">Humidity: ${data.main.humidity}%, Wind: ${data.wind.speed} m/s</p>
                            </div>
                        </div>
                    `;
                    document.getElementById('weather-info').innerHTML = html;
                } else {
                    document.getElementById('weather-info').innerHTML = '<p>Weather data unavailable.</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching weather data:', error);
                document.getElementById('weather-info').innerHTML = '<p>Failed to load weather data.</p>';
            });
    }
    
    // Load weather data initially
    loadWeatherData();
    
    // View sensor details function
    function viewSensorDetails(sensorId) {
        selectedSensor = sensors.find(s => s.id === sensorId);
        
        if (selectedSensor) {
            const aqi = selectedSensor.latest_reading ? selectedSensor.latest_reading.aqi : 0;
            const html = `
                <div class="bg-white bg-opacity-40 p-4 rounded-lg">
                    <h3 class="font-bold text-lg">${selectedSensor.name}</h3>
                    <p class="text-sm">${selectedSensor.location_name}</p>
                    
                    ${selectedSensor.latest_reading ? `
                        <div class="mt-4 ${getAqiClass(aqi)} p-3 rounded-lg text-center shadow-sm">
                            <span class="font-bold text-2xl">${aqi.toFixed(0)}</span><br>
                            <span>${selectedSensor.latest_reading.category}</span>
                        </div>
                        
                        <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                            <div class="bg-white bg-opacity-60 p-2 rounded-lg">
                                <p><span class="font-semibold">PM2.5:</span> ${selectedSensor.latest_reading.pm25 || 'N/A'}</p>
                                <p><span class="font-semibold">PM10:</span> ${selectedSensor.latest_reading.pm10 || 'N/A'}</p>
                                <p><span class="font-semibold">O3:</span> ${selectedSensor.latest_reading.o3 || 'N/A'}</p>
                            </div>
                            <div class="bg-white bg-opacity-60 p-2 rounded-lg">
                                <p><span class="font-semibold">CO:</span> ${selectedSensor.latest_reading.co || 'N/A'}</p>
                                <p><span class="font-semibold">NO2:</span> ${selectedSensor.latest_reading.no2 || 'N/A'}</p>
                                <p><span class="font-semibold">SO2:</span> ${selectedSensor.latest_reading.so2 || 'N/A'}</p>
                            </div>
                        </div>
                        
                        <div class="mt-3 bg-white bg-opacity-60 p-2 rounded-lg grid grid-cols-2 gap-3">
                            <p><span class="font-semibold">Temperature:</span> ${selectedSensor.latest_reading.temperature || 'N/A'}°C</p>
                            <p><span class="font-semibold">Humidity:</span> ${selectedSensor.latest_reading.humidity || 'N/A'}%</p>
                        </div>
                        
                        <p class="mt-3 text-xs">Last updated: ${new Date(selectedSensor.latest_reading.recorded_at).toLocaleString()}</p>
                    ` : '<p class="mt-2">No readings available for this sensor.</p>'}
                </div>
            `;
            
            document.getElementById('selected-sensor').innerHTML = html;
            
            // Load historical data
            loadSensorHistoricalData(sensorId, currentTimeframe);
        }
    }
    
    // Load historical data for a sensor
    function loadSensorHistoricalData(sensorId, timeframe) {
        fetch(`/sensor/${sensorId}/history?timeframe=${timeframe}`)
            .then(response => response.json())
            .then(data => {
                updateChart(data.labels, data.aqi, data.sensor.name);
                
                // Update timeframe buttons
                document.querySelectorAll('#timeframe-selector button').forEach(button => {
                    if (button.getAttribute('data-timeframe') === timeframe) {
                        button.classList.remove('bg-gray-300', 'text-gray-800');
                        button.classList.add('bg-blue-500', 'text-white');
                    } else {
                        button.classList.remove('bg-blue-500', 'text-white');
                        button.classList.add('bg-gray-300', 'text-gray-800');
                    }
                });
            })
            .catch(error => {
                console.error('Error loading historical data:', error);
            });
    }
    
    // Update chart
    function updateChart(labels, data, sensorName) {
        const ctx = document.getElementById('aqi-chart').getContext('2d');
        
        // Check if chart already exists
        if (chart) {
            chart.destroy();
        }
        
        // Get colors based on current theme
        const isDarkMode = document.documentElement.classList.contains('dark');
        const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
        const textColor = isDarkMode ? '#e5e5e5' : '#666';
        
        // Create chart
        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'AQI',
                    data: data,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: '#3b82f6',
                    pointRadius: 3,
                    pointHoverRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: isDarkMode ? '#333' : 'white',
                        titleColor: isDarkMode ? '#fff' : '#333',
                        bodyColor: isDarkMode ? '#ddd' : '#666',
                        borderColor: isDarkMode ? '#444' : '#ddd',
                        borderWidth: 1
                    },
                    title: {
                        display: true,
                        text: `AQI Trend for ${sensorName}`,
                        color: textColor,
                        font: {
                            size: 16
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: gridColor
                        },
                        ticks: {
                            color: textColor
                        }
                    },
                    x: {
                        grid: {
                            color: gridColor
                        },
                        ticks: {
                            color: textColor,
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });
    }
    
    // Set up timeframe selector click events
    document.querySelectorAll('#timeframe-selector button').forEach(button => {
        button.addEventListener('click', function() {
            const timeframe = this.getAttribute('data-timeframe');
            currentTimeframe = timeframe;
            
            if (selectedSensor) {
                loadSensorHistoricalData(selectedSensor.id, timeframe);
            }
        });
    });
    
    // Update chart when dark mode changes
    document.getElementById('dark-mode-toggle')?.addEventListener('change', function() {
        if (selectedSensor && chart) {
            loadSensorHistoricalData(selectedSensor.id, currentTimeframe);
        }
    });
</script>
@endsection 