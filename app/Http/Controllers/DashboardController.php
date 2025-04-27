<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use App\Models\AirQualityReading;
use App\Models\AlertThreshold;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    /**
     * Display the public dashboard
     */
    public function index()
    {
        $sensors = Sensor::with('latestReading')->where('status', 'active')->get();
        $alertThresholds = AlertThreshold::all();
        
        return view('dashboard', compact('sensors', 'alertThresholds'));
    }
    
    /**
     * Get historical data for a specific sensor
     */
    public function getSensorHistoricalData(Request $request, $sensorId)
    {
        $timeframe = $request->input('timeframe', 'day');
        
        $sensor = Sensor::findOrFail($sensorId);
        
        // Define the time ranges
        switch ($timeframe) {
            case 'week':
                $startDate = now()->subWeek();
                break;
            case 'month':
                $startDate = now()->subMonth();
                break;
            default: // day
                $startDate = now()->subDay();
                break;
        }
        
        $readings = AirQualityReading::where('sensor_id', $sensor->id)
            ->where('recorded_at', '>=', $startDate)
            ->orderBy('recorded_at')
            ->get();
            
        $data = [
            'labels' => $readings->pluck('recorded_at')->map(function ($date) {
                return $date->format('Y-m-d H:i');
            }),
            'aqi' => $readings->pluck('aqi'),
            'sensor' => $sensor,
        ];
        
        return response()->json($data);
    }
    
    /**
     * Get weather data from OpenWeather API
     */
    public function getWeatherData(Request $request)
    {
        $apiKey = config('services.openweather.key');
        $city = $request->input('city', 'Colombo');
        
        try {
            $response = Http::get("https://api.openweathermap.org/data/2.5/weather", [
                'q' => $city,
                'appid' => $apiKey,
                'units' => 'metric',
            ]);
            
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch weather data'], 500);
        }
    }
}
