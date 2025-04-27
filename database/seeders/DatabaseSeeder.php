<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Create admin user
        \App\Models\User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        
        // Create system admin user
        \App\Models\User::factory()->create([
            'name' => 'System Administrator',
            'email' => 'system@example.com',
            'password' => bcrypt('password'),
            'role' => 'system_admin',
        ]);
        
        // Seed alert thresholds
        $this->call(AlertThresholdSeeder::class);
        
        // Seed sensors and readings
        $this->call(SensorSeeder::class);
    }
}
