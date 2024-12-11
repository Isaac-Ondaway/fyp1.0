<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $primaryKey = 'batchID'; // Primary key
    public $incrementing = false;

    protected $fillable = [
        'batchID',
        'batchName',
        'batchStartDate',
    ];

    // A batch can have many programs
    public function programs()
    {
        return $this->hasMany(Program::class, 'batchID', 'batchID'); // 'batchID' in the programs table
    }

    public function programEntryLevels()
    {
        return $this->hasMany(ProgramEntryLevel::class, 'batch_id', 'batchID');
    }
}
