<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    // Add these fields to the $fillable array
    protected $fillable = [
        'title',
        'description',
        'start_datetime',
        'end_datetime',
        'color',
        'visibility',
        'all_day',
        'google_event_id',
    ];
}
