@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Manage Sensors</h1>
                    <a href="{{ route('admin.sensors.create') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                        Add New Sensor
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm font-semibold text-gray-600">ID</th>
                                <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm font-semibold text-gray-600">Name</th>
                                <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm font-semibold text-gray-600">Location</th>
                                <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm font-semibold text-gray-600">Coordinates</th>
                                <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm font-semibold text-gray-600">Status</th>
                                <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm font-semibold text-gray-600">Latest Reading</th>
                                <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm font-semibold text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sensors as $sensor)
                                <tr>
                                    <td class="py-2 px-4 border-b border-gray-200">{{ $sensor->sensor_id }}</td>
                                    <td class="py-2 px-4 border-b border-gray-200">{{ $sensor->name }}</td>
                                    <td class="py-2 px-4 border-b border-gray-200">{{ $sensor->location_name }}</td>
                                    <td class="py-2 px-4 border-b border-gray-200">{{ $sensor->latitude }}, {{ $sensor->longitude }}</td>
                                    <td class="py-2 px-4 border-b border-gray-200">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $sensor->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($sensor->status) }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-4 border-b border-gray-200">
                                        @if($sensor->latestReading)
                                            <span class="font-semibold">AQI: {{ $sensor->latestReading->aqi }}</span><br>
                                            <span class="text-xs text-gray-500">{{ $sensor->latestReading->recorded_at->diffForHumans() }}</span>
                                        @else
                                            <span class="text-gray-500">No readings</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-4 border-b border-gray-200">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.sensors.show', $sensor) }}" class="text-blue-500 hover:text-blue-700">
                                                View
                                            </a>
                                            <a href="{{ route('admin.sensors.edit', $sensor) }}" class="text-yellow-500 hover:text-yellow-700">
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.sensors.destroy', $sensor) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Are you sure you want to delete this sensor?')">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-4 px-4 border-b border-gray-200 text-center text-gray-500">
                                        No sensors found. <a href="{{ route('admin.sensors.create') }}" class="text-blue-500 hover:text-blue-700">Add a sensor</a>
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
@endsection 