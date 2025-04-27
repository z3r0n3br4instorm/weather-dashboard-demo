<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use App\Models\AirQualityReading;
use App\Models\AlertThreshold;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function dashboard()
    {
        $activeSensors = Sensor::where('status', 'active')->count();
        $inactiveSensors = Sensor::where('status', 'inactive')->count();
        $readingsToday = AirQualityReading::whereDate('recorded_at', now())->count();
        $adminUsers = User::count();
        
        return view('admin.dashboard', compact('activeSensors', 'inactiveSensors', 'readingsToday', 'adminUsers'));
    }
    
    /**
     * Show the data simulation panel
     */
    public function simulationPanel()
    {
        $sensors = Sensor::all();
        return view('admin.simulation', compact('sensors'));
    }
    
    /**
     * Start/stop data simulation
     */
    public function toggleSimulation(Request $request)
    {
        $simulation = $request->input('simulation_status', 'stop');
        $frequency = $request->input('frequency', 15); // Default 15 minutes
        
        // Store simulation settings in session
        session(['simulation_status' => $simulation]);
        session(['simulation_frequency' => $frequency]);
        
        return redirect()->back()->with('success', 'Simulation settings updated.');
    }
    
    /**
     * Generate simulated data
     */
    public function generateSimulatedData(Request $request)
    {
        $sensorIds = $request->input('sensor_ids', []);
        
        if (empty($sensorIds)) {
            return redirect()->back()->with('error', 'No sensors selected.');
        }
        
        foreach ($sensorIds as $sensorId) {
            $sensor = Sensor::find($sensorId);
            
            if (!$sensor || $sensor->status != 'active') {
                continue;
            }
            
            // Generate random AQI between 0 and 500
            $aqi = rand(0, 500);
            
            // Determine category based on AQI
            $category = $this->getAQICategory($aqi);
            
            // Create the reading
            AirQualityReading::create([
                'sensor_id' => $sensor->id,
                'aqi' => $aqi,
                'pm25' => rand(0, 500) / 10,
                'pm10' => rand(0, 500) / 10,
                'co' => rand(0, 150) / 10,
                'o3' => rand(0, 120) / 10,
                'no2' => rand(0, 100) / 10,
                'so2' => rand(0, 80) / 10,
                'temperature' => rand(200, 350) / 10, // 20-35 degrees
                'humidity' => rand(400, 900) / 10, // 40-90%
                'category' => $category,
                'recorded_at' => now(),
            ]);
        }
        
        return redirect()->back()->with('success', 'Generated simulated data for ' . count($sensorIds) . ' sensors.');
    }
    
    /**
     * Manage alert thresholds
     */
    public function alertThresholds()
    {
        $thresholds = AlertThreshold::all();
        return view('admin.alert_thresholds', compact('thresholds'));
    }
    
    /**
     * Update alert thresholds
     */
    public function updateAlertThresholds(Request $request)
    {
        $thresholds = $request->input('thresholds', []);
        
        foreach ($thresholds as $id => $threshold) {
            AlertThreshold::findOrFail($id)->update([
                'min_value' => $threshold['min_value'],
                'max_value' => $threshold['max_value'],
                'send_notification' => isset($threshold['send_notification']),
            ]);
        }
        
        return redirect()->back()->with('success', 'Alert thresholds updated successfully.');
    }
    
    /**
     * User management
     */
    public function userManagement()
    {
        $users = User::all();
        return view('admin.users', compact('users'));
    }
    
    /**
     * Create user
     */
    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,system_admin',
        ]);
        
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);
        
        return redirect()->back()->with('success', 'User created successfully.');
    }
    
    /**
     * Helper function to determine AQI category
     */
    private function getAQICategory($aqi)
    {
        if ($aqi <= 50) {
            return 'Good';
        } elseif ($aqi <= 100) {
            return 'Moderate';
        } elseif ($aqi <= 150) {
            return 'Unhealthy for Sensitive Groups';
        } elseif ($aqi <= 200) {
            return 'Unhealthy';
        } elseif ($aqi <= 300) {
            return 'Very Unhealthy';
        } else {
            return 'Hazardous';
        }
    }
}
