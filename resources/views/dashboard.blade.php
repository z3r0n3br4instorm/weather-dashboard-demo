@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-2xl font-bold mb-6">Air Quality Dashboard - Colombo Metropolitan Area</h1>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Map and sensor information -->
                    <div class="md:col-span-2">
                        <div id="map" class="map-container rounded-lg shadow"></div>
                        
                        <div class="mt-4">
                            <h2 class="text-lg font-semibold mb-2">AQI Legend</h2>
                            <div class="grid grid-cols-2 md:grid-cols-6 gap-2">
                                @foreach($alertThresholds as $threshold)
                                <div class="rounded px-2 py-1 text-center" style="background-color: {{ $threshold->color_code }}; color: {{ in_array($threshold->category, ['Unhealthy', 'Very Unhealthy', 'Hazardous']) ? 'white' : 'black' }}">
                                    {{ $threshold->category }}
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <!-- Current weather and selected sensor info -->
                    <div>
                        <div class="bg-gray-100 p-4 rounded-lg shadow mb-4">
                            <h2 class="text-lg font-semibold mb-2">Current Weather</h2>
                            <div id="weather-info">
                                <p class="text-gray-500">Loading weather data...</p>
                            </div>
                        </div>
                        
                        <div class="bg-gray-100 p-4 rounded-lg shadow">
                            <h2 class="text-lg font-semibold mb-2">Selected Sensor</h2>
                            <div id="selected-sensor">
                                <p class="text-gray-500">Click on a sensor in the map to view details.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Historical data chart -->
                <div class="mt-6">
                    <h2 class="text-lg font-semibold mb-2">Historical AQI Data</h2>
                    <div class="bg-gray-100 p-4 rounded-lg shadow">
                        <div id="chart-container" style="height: 300px;">
                            <canvas id="aqi-chart"></canvas>
                        </div>
                        
                        <div class="mt-4 text-center" id="timeframe-selector">
                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-4 rounded mx-1" data-timeframe="day">24 Hours</button>
                            <button class="bg-gray-300 hover:bg-blue-700 text-gray-800 hover:text-white font-bold py-1 px-4 rounded mx-1" data-timeframe="week">Week</button>
                            <button class="bg-gray-300 hover:bg-blue-700 text-gray-800 hover:text-white font-bold py-1 px-4 rounded mx-1" data-timeframe="month">Month</button>
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
            radius: 10,
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
                    <div class="mt-2 ${getAqiClass(aqi)} p-2 rounded text-center">
                        <span class="font-bold">AQI: ${aqi.toFixed(0)}</span><br>
                        <span>${sensor.latest_reading.category}</span>
                    </div>
                    <p class="mt-1 text-xs">Last updated: ${new Date(sensor.latest_reading.recorded_at).toLocaleString()}</p>
                ` : '<p>No readings available</p>'}
                <button class="mt-2 bg-blue-500 hover:bg-blue-700 text-white text-xs py-1 px-2 rounded w-full" onclick="viewSensorDetails(${sensor.id})">View Details</button>
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
                        <div class="flex items-center">
                            <img src="https://openweathermap.org/img/wn/${data.weather[0].icon}.png" alt="${data.weather[0].description}">
                            <div>
                                <p class="font-bold">${data.name}</p>
                                <p>${Math.round(data.main.temp)}°C, ${data.weather[0].description}</p>
                                <p>Humidity: ${data.main.humidity}%, Wind: ${data.wind.speed} m/s</p>
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
                <div>
                    <h3 class="font-bold">${selectedSensor.name}</h3>
                    <p class="text-sm">${selectedSensor.location_name}</p>
                    
                    ${selectedSensor.latest_reading ? `
                        <div class="mt-3 ${getAqiClass(aqi)} p-2 rounded text-center">
                            <span class="font-bold text-xl">${aqi.toFixed(0)}</span><br>
                            <span>${selectedSensor.latest_reading.category}</span>
                        </div>
                        
                        <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <p><span class="font-semibold">PM2.5:</span> ${selectedSensor.latest_reading.pm25 || 'N/A'}</p>
                                <p><span class="font-semibold">PM10:</span> ${selectedSensor.latest_reading.pm10 || 'N/A'}</p>
                                <p><span class="font-semibold">O3:</span> ${selectedSensor.latest_reading.o3 || 'N/A'}</p>
                            </div>
                            <div>
                                <p><span class="font-semibold">CO:</span> ${selectedSensor.latest_reading.co || 'N/A'}</p>
                                <p><span class="font-semibold">NO2:</span> ${selectedSensor.latest_reading.no2 || 'N/A'}</p>
                                <p><span class="font-semibold">SO2:</span> ${selectedSensor.latest_reading.so2 || 'N/A'}</p>
                            </div>
                        </div>
                        
                        <div class="mt-2">
                            <p><span class="font-semibold">Temperature:</span> ${selectedSensor.latest_reading.temperature || 'N/A'}°C</p>
                            <p><span class="font-semibold">Humidity:</span> ${selectedSensor.latest_reading.humidity || 'N/A'}%</p>
                        </div>
                        
                        <p class="mt-2 text-xs">Last updated: ${new Date(selectedSensor.latest_reading.recorded_at).toLocaleString()}</p>
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
        
        if (chart) {
            chart.destroy();
        }
        
        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: `AQI - ${sensorName}`,
                    data: data,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'AQI Value'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Time'
                        }
                    }
                }
            }
        });
    }
    
    // Event listeners for timeframe buttons
    document.querySelectorAll('#timeframe-selector button').forEach(button => {
        button.addEventListener('click', function() {
            const timeframe = this.getAttribute('data-timeframe');
            currentTimeframe = timeframe;
            
            if (selectedSensor) {
                loadSensorHistoricalData(selectedSensor.id, timeframe);
            }
        });
    });
</script>
@endsection 