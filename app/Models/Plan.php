<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'price', 'duration', 'max_users', 'durationtype', 'max_form', 'max_roles', 'max_booking', 'max_documents', 'max_polls', 'description'
    ];
}
