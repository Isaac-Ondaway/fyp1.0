<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterviewSchedule extends Model
{
    use HasFactory;

    protected $table = 'interview_schedule'; // Adjust the table name if needed

    protected $primaryKey = 'schedule_id'; // Set the primary key if it's different

    protected $fillable = [
        'interviewee_id',
        'program_id',
        'batch_id',
        'scheduled_date',
        'remarks',
        'status'
        ];

        protected $casts = [
            'scheduled_date' => 'datetime',
        ];
        
    /**
     * Relation with Interview
     */
    public function interview()
    {
        return $this->belongsTo(Interview::class, 'interviewee_id', 'interviewID');
    }

        public function interviewee()
    {
        return $this->belongsTo(Interview::class, 'interviewee_id', 'interviewID');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }



}
