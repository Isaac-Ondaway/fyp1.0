<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $primaryKey = 'rolesID';

    protected $fillable = ['id', 'type']; // Ensure these attributes are fillable if needed

    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id', 'rolesID', 'id');
    }
}
