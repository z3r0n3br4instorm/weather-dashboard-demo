<?php

namespace Database\Seeders;

use App\Models\AlertThreshold;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AlertThresholdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $thresholds = [
            [
                'category' => 'Good',
                'min_value' => 0,
                'max_value' => 50,
                'color_code' => '#00E400',
                'description' => 'Air quality is considered satisfactory, and air pollution poses little or no risk.',
                'send_notification' => false,
            ],
            [
                'category' => 'Moderate',
                'min_value' => 51,
                'max_value' => 100,
                'color_code' => '#FFFF00',
                'description' => 'Air quality is acceptable; however, for some pollutants, there may be a moderate health concern for a very small number of people who are unusually sensitive to air pollution.',
                'send_notification' => false,
            ],
            [
                'category' => 'Unhealthy for Sensitive Groups',
                'min_value' => 101,
                'max_value' => 150,
                'color_code' => '#FF7E00',
                'description' => 'Members of sensitive groups may experience health effects. The general public is not likely to be affected.',
                'send_notification' => true,
            ],
            [
                'category' => 'Unhealthy',
                'min_value' => 151,
                'max_value' => 200,
                'color_code' => '#FF0000',
                'description' => 'Everyone may begin to experience health effects; members of sensitive groups may experience more serious health effects.',
                'send_notification' => true,
            ],
            [
                'category' => 'Very Unhealthy',
                'min_value' => 201,
                'max_value' => 300,
                'color_code' => '#99004C',
                'description' => 'Health warnings of emergency conditions. The entire population is more likely to be affected.',
                'send_notification' => true,
            ],
            [
                'category' => 'Hazardous',
                'min_value' => 301,
                'max_value' => 500,
                'color_code' => '#7E0023',
                'description' => 'Health alert: everyone may experience more serious health effects.',
                'send_notification' => true,
            ],
        ];

        foreach ($thresholds as $threshold) {
            AlertThreshold::create($threshold);
        }
    }
}
