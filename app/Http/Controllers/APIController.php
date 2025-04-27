<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use App\Models\AirQualityReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class APIController extends Controller
{
    /**
     * Get all active sensors with their latest readings
     */
    public function getSensors()
    {
        $sensors = Sensor::with('latestReading')
            ->where('status', 'active')
            ->get();
            
        return response()->json($sensors);
    }
    
    /**
     * Get a specific sensor with its latest reading
     */
    public function getSensor($id)
    {
        $sensor = Sensor::with('latestReading')->findOrFail($id);
        return response()->json($sensor);
    }
    
    /**
     * Get historical readings for a sensor
     */
    public function getSensorHistory($id, Request $request)
    {
        $days = $request->input('days', 1);
        $limit = $request->input('limit', 100);
        
        $sensor = Sensor::findOrFail($id);
        
        $readings = AirQualityReading::where('sensor_id', $sensor->id)
            ->where('recorded_at', '>=', now()->subDays($days))
            ->orderBy('recorded_at', 'desc')
            ->limit($limit)
            ->get();
            
        return response()->json([
            'sensor' => $sensor,
            'readings' => $readings,
        ]);
    }
    
    /**
     * Get weather data from OpenWeather API
     */
    public function getWeatherData(Request $request)
    {
        $lat = $request->input('lat', 6.9271); // Default Colombo latitude
        $lon = $request->input('lon', 79.8612); // Default Colombo longitude
        $apiKey = config('services.openweather.key');
        
        try {
            $response = Http::get("https://api.openweathermap.org/data/2.5/weather", [
                'lat' => $lat,
                'lon' => $lon,
                'appid' => $apiKey,
                'units' => 'metric',
            ]);
            
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch weather data'], 500);
        }
    }
}
