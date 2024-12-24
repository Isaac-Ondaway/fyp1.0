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
        'email',
    ];

    /**
     * Relation with Program
     */
    public function program()
    {
        return $this->belongsTo(Program::class, 'programID', 'programID');
    }

    /**
     * Relation with Batch
     */
    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batchID', 'batchID');
    }

    /**
     * Relation with InterviewSchedule
     */
    public function interviewSchedule()
    {
        return $this->hasOne(InterviewSchedule::class, 'interviewee_id', 'interviewID');
    }
}
