<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $primaryKey = 'resourceID';
    
    public function resource()
    {
        return $this->belongsTo(Resource::class, 'resourceID');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'studentID');
    }

}
