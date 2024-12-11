<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id', 'id', 'rolesID');
    }

    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }
        $this->roles()->syncWithoutDetaching($role);
    }

    public function hasRole($roleName)
    {
        return $this->roles()->where('type', $roleName)->exists();
    }
    
    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'facultyID', 'id');
    }
    

    public function getFacultyNameAttribute()
    {
        return $this->faculty ? $this->faculty->name : 'Not Assigned';
    }
    
    public function programs()
    {
        return $this->hasMany(Program::class, 'facultyID', 'id');
    }
}
