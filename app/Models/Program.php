<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $table = 'programs';  // This is optional if the table name is 'programs'
    
    protected $primaryKey = 'programID'; // Define the primary key
    public $incrementing = false;  // Indicate that the primary key is not auto-incrementing
    protected $keyType = 'string'; // Define the primary key as a string

    protected $fillable = [
        'programID',
        'batchID',
        'facultyID',
        'programName',
        'programSem',
        'levelEdu',
        'NEC',
        'programFee',
        'programStatus',
        'programDesc',
    ];

    // Define the relationship with the User model function 
    public function faculty()
    {
        return $this->belongsTo(User::class, 'facultyID');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batchID', 'batchID');
    }

    // Define relationship to ProgramEntryLevel
    public function entryLevels()
    {
        return $this->hasMany(ProgramEntryLevel::class, 'programID', 'programID');
    }

    public function interviews()
    {
        return $this->hasMany(Interview::class, 'programID', 'programID');
    }
}
