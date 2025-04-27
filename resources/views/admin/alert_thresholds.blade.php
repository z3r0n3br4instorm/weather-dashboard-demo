@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-2xl font-bold mb-6">Alert Thresholds Management</h1>
                
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                Configure the AQI threshold values and notification settings for different air quality categories. These thresholds are used to classify air quality readings and determine when alerts should be displayed.
                            </p>
                        </div>
                    </div>
                </div>
                
                <form action="{{ route('admin.alerts.update') }}" method="POST">
                    @csrf
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm font-semibold text-gray-600">Category</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm font-semibold text-gray-600">Min Value</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm font-semibold text-gray-600">Max Value</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm font-semibold text-gray-600">Color</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm font-semibold text-gray-600">Send Notification</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-sm font-semibold text-gray-600">Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($thresholds as $threshold)
                                    <tr>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            {{ $threshold->category }}
                                            <input type="hidden" name="thresholds[{{ $threshold->id }}][category]" value="{{ $threshold->category }}">
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            <input type="number" name="thresholds[{{ $threshold->id }}][min_value]" value="{{ $threshold->min_value }}" min="0" max="500" class="shadow appearance-none border rounded w-full py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" {{ $threshold->category === 'Good' ? 'readonly' : '' }}>
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            <input type="number" name="thresholds[{{ $threshold->id }}][max_value]" value="{{ $threshold->max_value }}" min="0" max="500" class="shadow appearance-none border rounded w-full py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            <div class="w-8 h-8 rounded" style="background-color: {{ $threshold->color_code }}"></div>
                                            <input type="hidden" name="thresholds[{{ $threshold->id }}][color_code]" value="{{ $threshold->color_code }}">
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            <input type="checkbox" name="thresholds[{{ $threshold->id }}][send_notification]" {{ $threshold->send_notification ? 'checked' : '' }}>
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200 text-sm">
                                            {{ $threshold->description }}
                                            <input type="hidden" name="thresholds[{{ $threshold->id }}][description]" value="{{ $threshold->description }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Update Thresholds
                        </button>
                    </div>
                </form>
                
                <div class="mt-8">
                    <h2 class="text-lg font-semibold mb-4">AQI Legend Preview</h2>
                    
                    <div class="grid grid-cols-2 md:grid-cols-6 gap-2">
                        @foreach($thresholds as $threshold)
                            <div class="rounded px-2 py-1 text-center" style="background-color: {{ $threshold->color_code }}; color: {{ in_array($threshold->category, ['Unhealthy', 'Very Unhealthy', 'Hazardous']) ? 'white' : 'black' }}">
                                {{ $threshold->category }}
                                <div class="text-xs">{{ $threshold->min_value }}-{{ $threshold->max_value }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection