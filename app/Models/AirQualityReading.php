<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AirQualityReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'sensor_id',
        'aqi',
        'pm25',
        'pm10',
        'co',
        'o3',
        'no2',
        'so2',
        'temperature',
        'humidity',
        'category',
        'recorded_at',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    /**
     * Get the sensor that owns this reading
     */
    public function sensor(): BelongsTo
    {
        return $this->belongsTo(Sensor::class);
    }
}
