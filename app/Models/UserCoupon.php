<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCoupon extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user',
        'coupon',
        'userrequest',
    ];

    public function userDetail()
    {
        return $this->hasOne('App\Models\User', 'id', 'user');
    }

    public function couponDetail()
    {
        return $this->hasOne('App\Models\Coupon', 'id', 'coupon');
    }
}
