<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    use HasFactory;

    protected $table = 'interviews';
    protected $primaryKey = 'interviewID';

    protected $fillable = [
        'programID',
        'batchID',
        'intervieweeName',
        'contactNumber',
        'interviewStatus'
    ];

    // Relation with Program
    public function program()
    {
        return $this->belongsTo(Program::class, 'programID', 'programID');
    }

    // Relation with Batch via Program
    public function batch()
    {
        return $this->program->batch();
    }
}
