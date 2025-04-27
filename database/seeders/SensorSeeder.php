<?php

namespace Database\Seeders;

use App\Models\Sensor;
use App\Models\AirQualityReading;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SensorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample sensors for Colombo area
        $sensors = [
            [
                'sensor_id' => 'CLB-FORT-001',
                'name' => 'Colombo Fort',
                'location_name' => 'Colombo Fort Station',
                'description' => 'Central monitoring station located in Colombo Fort',
                'latitude' => 6.9271,
                'longitude' => 79.8612,
                'status' => 'active',
            ],
            [
                'sensor_id' => 'PTH-MKT-002',
                'name' => 'Pettah Market',
                'location_name' => 'Pettah Commercial Area',
                'description' => 'High traffic and commercial activity area in Pettah',
                'latitude' => 6.9344,
                'longitude' => 79.8548,
                'status' => 'active',
            ],
            [
                'sensor_id' => 'DHW-RES-003',
                'name' => 'Dehiwala',
                'location_name' => 'Dehiwala Residential',
                'description' => 'Residential area monitoring in Dehiwala suburb',
                'latitude' => 6.8504,
                'longitude' => 79.8651,
                'status' => 'active',
            ],
            [
                'sensor_id' => 'RML-IND-004',
                'name' => 'Ratmalana Industrial',
                'location_name' => 'Ratmalana Industrial Zone',
                'description' => 'Monitoring station in industrial area of Ratmalana',
                'latitude' => 6.8194,
                'longitude' => 79.8828,
                'status' => 'active',
            ],
            [
                'sensor_id' => 'MTL-BCH-005',
                'name' => 'Mount Lavinia',
                'location_name' => 'Mount Lavinia Beach Area',
                'description' => 'Coastal air quality monitoring station',
                'latitude' => 6.8295,
                'longitude' => 79.8634,
                'status' => 'active',
            ]
        ];

        foreach ($sensors as $sensorData) {
            $sensor = Sensor::create($sensorData);
            
            // Generate some sample readings for each sensor
            $this->generateReadings($sensor->id);
        }
    }
    
    /**
     * Generate sample readings for a sensor
     */
    private function generateReadings($sensorId)
    {
        // Generate 24 hours of hourly readings
        $startTime = Carbon::now()->subHours(24);
        
        for ($i = 0; $i < 24; $i++) {
            $time = $startTime->copy()->addHours($i);
            
            // Generate random but realistic values
            $pm25 = rand(5, 80);
            $pm10 = $pm25 + rand(5, 30);
            $o3 = rand(20, 150) / 10;
            $co = rand(5, 150) / 10;
            $no2 = rand(10, 100);
            $so2 = rand(5, 50);
            
            // Calculate AQI based on PM2.5 (simplified)
            $aqi = $this->calculateAQI($pm25);
            
            // Determine category
            $category = $this->getCategory($aqi);
            
            AirQualityReading::create([
                'sensor_id' => $sensorId,
                'aqi' => $aqi,
                'category' => $category,
                'pm25' => $pm25,
                'pm10' => $pm10,
                'o3' => $o3,
                'co' => $co,
                'no2' => $no2,
                'so2' => $so2,
                'temperature' => rand(240, 340) / 10, // 24.0 - 34.0
                'humidity' => rand(50, 90),
                'recorded_at' => $time,
            ]);
        }
    }
    
    /**
     * Simplified AQI calculation based on PM2.5
     */
    private function calculateAQI($pm25)
    {
        // Simple formula (not exact EPA method, but reasonable approximation)
        if ($pm25 <= 12) {
            return round(($pm25 / 12) * 50);
        } else if ($pm25 <= 35.4) {
            return round(((($pm25 - 12) / (35.4 - 12)) * (100 - 51)) + 51);
        } else if ($pm25 <= 55.4) {
            return round(((($pm25 - 35.4) / (55.4 - 35.4)) * (150 - 101)) + 101);
        } else if ($pm25 <= 150.4) {
            return round(((($pm25 - 55.4) / (150.4 - 55.4)) * (200 - 151)) + 151);
        } else if ($pm25 <= 250.4) {
            return round(((($pm25 - 150.4) / (250.4 - 150.4)) * (300 - 201)) + 201);
        } else {
            return round(((($pm25 - 250.4) / (350.4 - 250.4)) * (400 - 301)) + 301);
        }
    }
    
    /**
     * Get AQI category based on value
     */
    private function getCategory($aqi)
    {
        if ($aqi <= 50) return 'Good';
        if ($aqi <= 100) return 'Moderate';
        if ($aqi <= 150) return 'Unhealthy for Sensitive Groups';
        if ($aqi <= 200) return 'Unhealthy';
        if ($aqi <= 300) return 'Very Unhealthy';
        return 'Hazardous';
    }
} 