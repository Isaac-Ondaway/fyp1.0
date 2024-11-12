<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramEntryLevel extends Model
{
    use HasFactory;

    protected $table = 'program_entry_levels';

    protected $primaryKey = 'pel_id'; // primary key

    protected $fillable = [
        'program_id', 
        'batch_id', 
        'entry_level_id', 
        'intake_count'
    ];
    

    // Define relationship to Program
    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'programID');
    }

    // Define relationship to EntryLevelCategory
    public function entryLevelCategory()
    {
        return $this->belongsTo(EntryLevelCategory::class, 'entryLevelCategoryID');
    }
    
    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id', 'batchID');
    }

    public function entryLevel()
    {
        return $this->belongsTo(EntryLevel::class, 'entry_level_id', 'entryLevelID');
    }

}

