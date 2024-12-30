<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramEntryLevelMapping extends Model
{
    use HasFactory;

    protected $table = 'program_entry_level_mapping';

    protected $fillable = ['programID', 'batchID', 'entry_level_category_id', 'is_offered'];

    // public function program()
    // {
    //     return $this->belongsTo(Program::class, ['programID', 'batchID'], ['programID', 'batchID']);
    // }

    public function program()
    {
        return $this->belongsTo(Program::class, 'programID', 'programID')
            ->whereColumn('batchID', 'batchID');
    }


    public function entryLevelCategory()
    {
        return $this->belongsTo(EntryLevelCategory::class, 'entry_level_category_id', 'entryLevelCategoryID');
    }
}
