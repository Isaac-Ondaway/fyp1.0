<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $primaryKey = 'batchID'; // Primary key

    protected $fillable = [
        'batchName',
        'batchStartDate',
    ];

    // A batch can have many programs
    public function programs()
    {
        return $this->hasMany(Program::class, 'batchID', 'batchID'); // 'batchID' in the programs table
    }
}
