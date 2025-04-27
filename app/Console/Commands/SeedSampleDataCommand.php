<?php

namespace App\Console\Commands;

use App\Models\Sensor;
use App\Models\AirQualityReading;
use Illuminate\Console\Command;

class SeedSampleDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:sample-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed sample sensors and air quality readings for Colombo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Seeding sample data for Colombo...');
        
        // Sample sensor locations in Colombo
        $sensorLocations = [
            [
                'sensor_id' => 'CLB001',
                'name' => 'Colombo Fort',
                'location_name' => 'Central Colombo',
                'latitude' => 6.9271,
                'longitude' => 79.8612,
                'description' => 'Central business district sensor',
            ],
            [
                'sensor_id' => 'CLB002',
                'name' => 'Mount Lavinia',
                'location_name' => 'Southern Colombo',
                'latitude' => 6.8309,
                'longitude' => 79.8641,
                'description' => 'Coastal area sensor',
            ],
            [
                'sensor_id' => 'CLB003',
                'name' => 'Dehiwala',
                'location_name' => 'Dehiwala-Mount Lavinia',
                'latitude' => 6.8570,
                'longitude' => 79.8650,
                'description' => 'Residential zone sensor',
            ],
            [
                'sensor_id' => 'CLB004',
                'name' => 'Kollupitiya',
                'location_name' => 'Central Colombo',
                'latitude' => 6.9009,
                'longitude' => 79.8512,
                'description' => 'Commercial zone sensor',
            ],
            [
                'sensor_id' => 'CLB005',
                'name' => 'Moratuwa',
                'location_name' => 'Southern Colombo',
                'latitude' => 6.7871,
                'longitude' => 79.8830,
                'description' => 'Suburban area sensor',
            ],
            [
                'sensor_id' => 'CLB006',
                'name' => 'Kelaniya',
                'location_name' => 'Eastern Colombo',
                'latitude' => 6.9552,
                'longitude' => 79.9214,
                'description' => 'Industrial zone sensor',
            ],
            [
                'sensor_id' => 'CLB007',
                'name' => 'Rajagiriya',
                'location_name' => 'Eastern Colombo',
                'latitude' => 6.9096,
                'longitude' => 79.8882,
                'description' => 'Urban traffic sensor',
            ],
            [
                'sensor_id' => 'CLB008',
                'name' => 'Battaramulla',
                'location_name' => 'Eastern Colombo',
                'latitude' => 6.8996,
                'longitude' => 79.9186,
                'description' => 'Administrative zone sensor',
            ],
        ];
        
        // Create sample sensors
        $this->info('Creating sensors...');
        $sensors = [];
        $progressBar = $this->output->createProgressBar(count($sensorLocations));
        
        foreach ($sensorLocations as $location) {
            $sensor = Sensor::firstOrCreate(
                ['sensor_id' => $location['sensor_id']],
                [
                    'name' => $location['name'],
                    'location_name' => $location['location_name'],
                    'latitude' => $location['latitude'],
                    'longitude' => $location['longitude'],
                    'status' => 'active',
                    'description' => $location['description'],
                ]
            );
            
            $sensors[] = $sensor;
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
        
        // Generate readings for the past 7 days
        $this->info('Generating sample readings for the past 7 days...');
        $totalReadings = count($sensors) * 7 * 24; // sensors * days * hours
        $progressBar = $this->output->createProgressBar($totalReadings);
        
        foreach ($sensors as $sensor) {
            // Generate readings for each hour of the past 7 days
            for ($day = 7; $day >= 0; $day--) {
                for ($hour = 0; $hour < 24; $hour++) {
                    $timestamp = now()->subDays($day)->setHour($hour)->setMinute(0)->setSecond(0);
                    
                    // Generate random but realistic AQI (with daily patterns)
                    $baseAqi = $this->getBaseAqi($sensor->id, $hour);
                    $aqi = max(0, min(500, $baseAqi + rand(-20, 20)));
                    
                    // Determine category based on AQI
                    $category = $this->getAQICategory($aqi);
                    
                    // Create the reading
                    AirQualityReading::updateOrCreate(
                        [
                            'sensor_id' => $sensor->id,
                            'recorded_at' => $timestamp,
                        ],
                        [
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
                        ]
                    );
                    
                    $progressBar->advance();
                }
            }
        }
        
        $progressBar->finish();
        $this->newLine();
        
        $this->info('Sample data seeded successfully!');
        
        return Command::SUCCESS;
    }
    
    /**
     * Get a realistic base AQI based on sensor location and time of day
     */
    private function getBaseAqi(int $sensorId, int $hour): int
    {
        // Morning and evening rush hours have higher AQI
        $rushHour = ($hour >= 7 && $hour <= 9) || ($hour >= 16 && $hour <= 18);
        
        // Night has lower AQI
        $night = $hour >= 22 || $hour <= 5;
        
        // Industrial and urban areas have higher baseline AQI
        $urbanOrIndustrial = in_array($sensorId, [1, 4, 6, 7]);
        
        // Base AQI for different conditions
        if ($urbanOrIndustrial && $rushHour) {
            return rand(120, 180); // Urban areas during rush hour
        } elseif ($urbanOrIndustrial) {
            return rand(80, 120); // Urban areas normal hours
        } elseif ($rushHour) {
            return rand(60, 100); // Suburban areas during rush hour
        } elseif ($night) {
            return rand(30, 50); // Night time
        } else {
            return rand(40, 80); // Default case
        }
    }
    
    /**
     * Determine AQI category based on value
     */
    private function getAQICategory(int $aqi): string
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
