<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertThreshold extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'min_value',
        'max_value',
        'color_code',
        'description',
        'send_notification',
    ];
}
