<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingPoll extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'vote',
        'poll_id',
        'location',
        'name',
        'session_id'
    ];
}
