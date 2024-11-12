<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntryLevelCategory extends Model
{
    use HasFactory;

    // Specify the table name (optional if it follows Laravel's naming convention)
    protected $table = 'entry_level_categories';

    // Specify the primary key
    protected $primaryKey = 'entryLevelCategoryID';

    // Allow mass assignment on the following fields
    protected $fillable = [
        'entryLevelID',
        'categoryName',
    ];

    // Define relationship to ProgramEntryLevel
    public function programEntryLevels()
    {
        return $this->hasMany(ProgramEntryLevel::class, 'entryLevelCategoryID', 'entryLevelCategoryID');
    }
}
