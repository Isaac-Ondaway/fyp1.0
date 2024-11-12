<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationToken extends Model
{
    use HasFactory;

    protected $fillable = ['token', 'expires_at', 'used', 'created_by'];
}
