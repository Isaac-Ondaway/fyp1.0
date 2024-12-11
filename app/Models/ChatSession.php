<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatSession extends Model
{
    use HasFactory;

    protected $table = 'chat_sessions'; // Ensure correct table name
    protected $primaryKey = 'session_id'; // Specify the primary key
    protected $fillable = ['user_id'];

    public function chats()
    {
        return $this->hasMany(Chat::class, 'session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}


