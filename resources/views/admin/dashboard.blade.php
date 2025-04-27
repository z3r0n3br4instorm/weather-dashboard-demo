@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-2xl font-bold mb-6">Administration Dashboard</h1>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <!-- Active Sensors Card -->
                    <div class="bg-green-100 p-4 rounded-lg shadow">
                        <h2 class="text-lg font-semibold text-green-700">Active Sensors</h2>
                        <p class="text-3xl font-bold text-green-800">{{ $activeSensors }}</p>
                    </div>
                    
                    <!-- Inactive Sensors Card -->
                    <div class="bg-gray-100 p-4 rounded-lg shadow">
                        <h2 class="text-lg font-semibold text-gray-700">Inactive Sensors</h2>
                        <p class="text-3xl font-bold text-gray-800">{{ $inactiveSensors }}</p>
                    </div>
                    
                    <!-- Readings Today Card -->
                    <div class="bg-blue-100 p-4 rounded-lg shadow">
                        <h2 class="text-lg font-semibold text-blue-700">Readings Today</h2>
                        <p class="text-3xl font-bold text-blue-800">{{ $readingsToday }}</p>
                    </div>
                    
                    <!-- Admin Users Card -->
                    <div class="bg-purple-100 p-4 rounded-lg shadow">
                        <h2 class="text-lg font-semibold text-purple-700">Admin Users</h2>
                        <p class="text-3xl font-bold text-purple-800">{{ $adminUsers }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Quick Actions -->
                    <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                        <h2 class="text-lg font-semibold mb-4">Quick Actions</h2>
                        <div class="space-y-2">
                            <a href="{{ route('admin.sensors.index') }}" class="block w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded text-center">
                                Manage Sensors
                            </a>
                            <a href="{{ route('admin.sensors.create') }}" class="block w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded text-center">
                                Add New Sensor
                            </a>
                            <a href="{{ route('admin.simulation') }}" class="block w-full bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded text-center">
                                Data Simulation
                            </a>
                            <a href="{{ route('admin.alerts') }}" class="block w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded text-center">
                                Manage Alert Thresholds
                            </a>
                            @if(auth()->user()->role === 'system_admin')
                                <a href="{{ route('admin.users') }}" class="block w-full bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded text-center">
                                    Manage Users
                                </a>
                            @endif
                        </div>
                    </div>
                    
                    <!-- System Status -->
                    <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                        <h2 class="text-lg font-semibold mb-4">System Status</h2>
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium">Sensor Network Status</span>
                                    <span class="text-sm font-medium text-green-500">Online</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-green-500 h-2.5 rounded-full" style="width: 100%"></div>
                                </div>
                            </div>
                            
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium">Data Collection</span>
                                    <span class="text-sm font-medium text-green-500">Active</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-green-500 h-2.5 rounded-full" style="width: 100%"></div>
                                </div>
                            </div>
                            
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium">Database Usage</span>
                                    <span class="text-sm font-medium">45%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-blue-500 h-2.5 rounded-full" style="width: 45%"></div>
                                </div>
                            </div>
                            
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium">System Load</span>
                                    <span class="text-sm font-medium">22%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-green-500 h-2.5 rounded-full" style="width: 22%"></div>
                                </div>
                            </div>
                            
                            <div class="pt-4">
                                <p class="text-sm text-gray-600">Last system update: {{ now()->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 