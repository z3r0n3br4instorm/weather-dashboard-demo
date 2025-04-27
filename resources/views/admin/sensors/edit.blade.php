@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold">Edit Sensor: {{ $sensor->name }}</h1>
                    <a href="{{ route('admin.sensors.index') }}" class="text-blue-500 hover:text-blue-700">
                        &larr; Back to Sensors List
                    </a>
                </div>
                
                <form action="{{ route('admin.sensors.update', $sensor) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <label for="sensor_id" class="block text-gray-700 text-sm font-bold mb-2">
                                    Sensor ID *
                                </label>
                                <input type="text" name="sensor_id" id="sensor_id" value="{{ old('sensor_id', $sensor->sensor_id) }}"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('sensor_id') border-red-500 @enderror"
                                    required>
                                @error('sensor_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">
                                    Name *
                                </label>
                                <input type="text" name="name" id="name" value="{{ old('name', $sensor->name) }}"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror"
                                    required>
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label for="location_name" class="block text-gray-700 text-sm font-bold mb-2">
                                    Location Name *
                                </label>
                                <input type="text" name="location_name" id="location_name" value="{{ old('location_name', $sensor->location_name) }}"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('location_name') border-red-500 @enderror"
                                    required>
                                @error('location_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div>
                            <div class="mb-4">
                                <label for="latitude" class="block text-gray-700 text-sm font-bold mb-2">
                                    Latitude *
                                </label>
                                <input type="number" step="0.0000001" name="latitude" id="latitude" value="{{ old('latitude', $sensor->latitude) }}"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('latitude') border-red-500 @enderror"
                                    required>
                                @error('latitude')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label for="longitude" class="block text-gray-700 text-sm font-bold mb-2">
                                    Longitude *
                                </label>
                                <input type="number" step="0.0000001" name="longitude" id="longitude" value="{{ old('longitude', $sensor->longitude) }}"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('longitude') border-red-500 @enderror"
                                    required>
                                @error('longitude')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label for="status" class="block text-gray-700 text-sm font-bold mb-2">
                                    Status *
                                </label>
                                <select name="status" id="status"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('status') border-red-500 @enderror"
                                    required>
                                    <option value="active" {{ old('status', $sensor->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $sensor->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label for="description" class="block text-gray-700 text-sm font-bold mb-2">
                            Description
                        </label>
                        <textarea name="description" id="description" rows="4"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description') border-red-500 @enderror">{{ old('description', $sensor->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div id="map" class="map-container rounded-lg shadow mb-6"></div>
                    
                    <div class="flex items-center justify-between">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Update Sensor
                        </button>
                        
                        <a href="{{ route('admin.sensors.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Cancel
                        </a>
                    </div>
                </form>
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
    
    // Create a marker
    let marker = L.marker([{{ $sensor->latitude }}, {{ $sensor->longitude }}], {
        draggable: true
    }).addTo(map);
    
    // Update lat/lng fields when marker is dragged
    marker.on('dragend', function(event) {
        const position = marker.getLatLng();
        document.getElementById('latitude').value = position.lat.toFixed(7);
        document.getElementById('longitude').value = position.lng.toFixed(7);
    });
    
    // Update marker position when lat/lng fields change
    document.getElementById('latitude').addEventListener('change', updateMarkerPosition);
    document.getElementById('longitude').addEventListener('change', updateMarkerPosition);
    
    function updateMarkerPosition() {
        const lat = parseFloat(document.getElementById('latitude').value);
        const lng = parseFloat(document.getElementById('longitude').value);
        
        if (!isNaN(lat) && !isNaN(lng)) {
            marker.setLatLng([lat, lng]);
            map.panTo([lat, lng]);
        }
    }
</script>
@endsection 