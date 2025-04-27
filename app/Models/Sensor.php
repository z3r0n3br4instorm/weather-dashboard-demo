<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sensor extends Model
{
    use HasFactory;

    protected $fillable = [
        'sensor_id',
        'name',
        'location_name',
        'latitude',
        'longitude',
        'status',
        'description',
    ];

    /**
     * Get all air quality readings for this sensor
     */
    public function airQualityReadings(): HasMany
    {
        return $this->hasMany(AirQualityReading::class);
    }

    /**
     * Get the most recent air quality reading for this sensor
     */
    public function latestReading()
    {
        return $this->hasOne(AirQualityReading::class)->latest('recorded_at');
    }
}
