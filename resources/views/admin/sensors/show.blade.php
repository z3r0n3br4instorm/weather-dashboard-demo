@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold">Sensor Details: {{ $sensor->name }}</h1>
                    <a href="{{ route('admin.sensors.index') }}" class="text-blue-500 hover:text-blue-700">
                        &larr; Back to Sensors List
                    </a>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Sensor Information -->
                    <div class="md:col-span-1">
                        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                            <h2 class="text-lg font-semibold mb-4">Sensor Information</h2>
                            
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6">
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Sensor ID</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $sensor->sensor_id }}</dd>
                                </div>
                                
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $sensor->name }}</dd>
                                </div>
                                
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Location</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $sensor->location_name }}</dd>
                                </div>
                                
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Coordinates</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $sensor->latitude }}, {{ $sensor->longitude }}</dd>
                                </div>
                                
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1 text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $sensor->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($sensor->status) }}
                                        </span>
                                    </dd>
                                </div>
                                
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Created At</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $sensor->created_at->format('Y-m-d H:i:s') }}</dd>
                                </div>
                                
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $sensor->updated_at->format('Y-m-d H:i:s') }}</dd>
                                </div>
                                
                                @if($sensor->description)
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $sensor->description }}</dd>
                                </div>
                                @endif
                            </dl>
                            
                            <div class="mt-6 flex space-x-3">
                                <a href="{{ route('admin.sensors.edit', $sensor) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Edit Sensor
                                </a>
                                
                                <form action="{{ route('admin.sensors.destroy', $sensor) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this sensor?')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Delete Sensor
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Map -->
                    <div class="md:col-span-2">
                        <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                            <h2 class="text-lg font-semibold mb-4">Location</h2>
                            <div id="map" class="map-container rounded-lg"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Latest Readings -->
                <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">Recent Readings</h2>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm font-semibold text-gray-600">Date &amp; Time</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm font-semibold text-gray-600">AQI</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm font-semibold text-gray-600">Category</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm font-semibold text-gray-600">PM2.5</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm font-semibold text-gray-600">PM10</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm font-semibold text-gray-600">Temperature</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm font-semibold text-gray-600">Humidity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sensor->airQualityReadings()->latest('recorded_at')->take(10)->get() as $reading)
                                    <tr>
                                        <td class="py-2 px-4 border-b border-gray-200">{{ $reading->recorded_at->format('Y-m-d H:i:s') }}</td>
                                        <td class="py-2 px-4 border-b border-gray-200 font-semibold">{{ $reading->aqi }}</td>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($reading->aqi <= 50) bg-green-100 text-green-800
                                                @elseif($reading->aqi <= 100) bg-yellow-100 text-yellow-800
                                                @elseif($reading->aqi <= 150) bg-orange-100 text-orange-800
                                                @elseif($reading->aqi <= 200) bg-red-100 text-red-800
                                                @elseif($reading->aqi <= 300) bg-purple-100 text-purple-800
                                                @else bg-gray-900 text-white
                                                @endif">
                                                {{ $reading->category }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200">{{ $reading->pm25 ?: 'N/A' }}</td>
                                        <td class="py-2 px-4 border-b border-gray-200">{{ $reading->pm10 ?: 'N/A' }}</td>
                                        <td class="py-2 px-4 border-b border-gray-200">{{ $reading->temperature ? $reading->temperature . '°C' : 'N/A' }}</td>
                                        <td class="py-2 px-4 border-b border-gray-200">{{ $reading->humidity ? $reading->humidity . '%' : 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-4 px-4 border-b border-gray-200 text-center text-gray-500">
                                            No readings available for this sensor.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Initialize map
    const map = L.map('map').setView([{{ $sensor->latitude }}, {{ $sensor->longitude }}], 14);
    
    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Add marker
    L.marker([{{ $sensor->latitude }}, {{ $sensor->longitude }}])
        .addTo(map)
        .bindPopup("<b>{{ $sensor->name }}</b><br>{{ $sensor->location_name }}")
        .openPopup();
</script>
@endsection 