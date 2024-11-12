<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntryLevel extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'entry_levels';

    // Specify the primary key
    protected $primaryKey = 'entryLevelID';

    // Allow mass assignment on the following fields
    protected $fillable = [
        'entryLevelName',
    ];

    // Define relationship to EntryLevelCategory
    public function categories()
    {
        return $this->hasMany(EntryLevelCategory::class, 'entryLevelID', 'entryLevelID');
    }

    // Define relationship to ProgramEntryLevel
    public function programEntryLevels()
    {
        return $this->hasMany(ProgramEntryLevel::class, 'entry_level_id', 'entryLevelID');
    }
}
