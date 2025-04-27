@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-2xl font-bold mb-6">Data Simulation Management</h1>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Simulation Settings -->
                    <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                        <h2 class="text-lg font-semibold mb-4">Simulation Settings</h2>
                        
                        <form action="{{ route('admin.simulation.toggle') }}" method="POST">
                            @csrf
                            
                            <div class="mb-4">
                                <label for="simulation_status" class="block text-gray-700 text-sm font-bold mb-2">
                                    Simulation Status
                                </label>
                                <select name="simulation_status" id="simulation_status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="start" {{ session('simulation_status') === 'start' ? 'selected' : '' }}>Start Simulation</option>
                                    <option value="stop" {{ session('simulation_status') !== 'start' ? 'selected' : '' }}>Stop Simulation</option>
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label for="frequency" class="block text-gray-700 text-sm font-bold mb-2">
                                    Data Generation Frequency (minutes)
                                </label>
                                <input type="number" name="frequency" id="frequency" min="1" max="60" value="{{ session('simulation_frequency', 15) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <p class="text-sm text-gray-500 mt-1">How often new data should be generated (1-60 minutes)</p>
                            </div>
                            
                            <div class="mt-6">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    Update Simulation Settings
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Manual Data Generation -->
                    <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                        <h2 class="text-lg font-semibold mb-4">Manual Data Generation</h2>
                        
                        <form action="{{ route('admin.simulation.generate') }}" method="POST">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Select Sensors for Data Generation
                                </label>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-64 overflow-y-auto p-2 border rounded">
                                    @foreach($sensors as $sensor)
                                        <div class="flex items-center">
                                            <input type="checkbox" name="sensor_ids[]" id="sensor_{{ $sensor->id }}" value="{{ $sensor->id }}" class="mr-2" {{ $sensor->status !== 'active' ? 'disabled' : '' }}>
                                            <label for="sensor_{{ $sensor->id }}" class="{{ $sensor->status !== 'active' ? 'text-gray-400' : '' }}">
                                                {{ $sensor->name }} ({{ $sensor->location_name }})
                                                @if($sensor->status !== 'active')
                                                    <span class="text-xs text-red-500">- Inactive</span>
                                                @endif
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="mt-2">
                                    <button type="button" id="select-all" class="text-sm text-blue-500 hover:text-blue-700">Select All Active</button> | 
                                    <button type="button" id="deselect-all" class="text-sm text-blue-500 hover:text-blue-700">Deselect All</button>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    Generate Data Now
                                </button>
                                <p class="text-sm text-gray-500 mt-1">This will immediately generate random air quality data for the selected sensors.</p>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Simulation Information -->
                <div class="mt-6 bg-white p-4 rounded-lg shadow border border-gray-200">
                    <h2 class="text-lg font-semibold mb-4">About Data Simulation</h2>
                    
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    This page allows you to simulate air quality data for testing purposes. The system can automatically generate data at specified intervals or you can manually generate data for selected sensors.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-md font-medium">Simulated Data Parameters</h3>
                            <ul class="list-disc pl-5 mt-2 text-sm text-gray-600">
                                <li>AQI (Air Quality Index): 0-500</li>
                                <li>PM2.5 and PM10: 0-500 μg/m³</li>
                                <li>CO (Carbon Monoxide): 0-15 ppm</li>
                                <li>O3 (Ozone): 0-12 ppm</li>
                                <li>NO2 (Nitrogen Dioxide): 0-10 ppm</li>
                                <li>SO2 (Sulfur Dioxide): 0-8 ppm</li>
                                <li>Temperature: 20-35°C</li>
                                <li>Humidity: 40-90%</li>
                            </ul>
                        </div>
                        
                        <div>
                            <h3 class="text-md font-medium">AQI Categories</h3>
                            <div class="grid grid-cols-2 md:grid-cols-6 gap-2 mt-2">
                                <div class="rounded px-2 py-1 text-center bg-green-500 text-white text-xs">
                                    Good (0-50)
                                </div>
                                <div class="rounded px-2 py-1 text-center bg-yellow-500 text-white text-xs">
                                    Moderate (51-100)
                                </div>
                                <div class="rounded px-2 py-1 text-center bg-orange-500 text-white text-xs">
                                    Unhealthy for Sensitive Groups (101-150)
                                </div>
                                <div class="rounded px-2 py-1 text-center bg-red-500 text-white text-xs">
                                    Unhealthy (151-200)
                                </div>
                                <div class="rounded px-2 py-1 text-center bg-purple-500 text-white text-xs">
                                    Very Unhealthy (201-300)
                                </div>
                                <div class="rounded px-2 py-1 text-center bg-gray-800 text-white text-xs">
                                    Hazardous (301-500)
                                </div>
                            </div>
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
    // Select/deselect all checkboxes
    document.getElementById('select-all').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('input[name="sensor_ids[]"]:not(:disabled)');
        checkboxes.forEach(checkbox => checkbox.checked = true);
    });
    
    document.getElementById('deselect-all').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('input[name="sensor_ids[]"]');
        checkboxes.forEach(checkbox => checkbox.checked = false);
    });
</script>
@endsection 