<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $primaryKey = 'bookingID'; // Set this to the primary key of your bookings table
    protected $fillable = [
        'resourceID',
        'studentID', 
        'programName',
        'phoneNo',
        'numberOfParticipant',
        'matricNo',
        'start_time',
        'end_time',
        'status',
    ];
    
    
    // Define the relationship with Resource
    public function resource()
    {
        return $this->belongsTo(Resource::class, 'resourceID');
    }

    // Define the relationship with User (Student)
    public function student()
    {
        return $this->belongsTo(User::class, 'studentID');
    }
}


