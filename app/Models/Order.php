<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'plan_id',
        'user_id',
        'stripe_payment_id',
        'amount',
        'status',
        'request_user_id',
        'payment_id',
        'coupon_code',
        'discount_amount',
        'paymet_type'
    ];

    public function Plan()
    {
        return $this->hasOne('App\Models\Plan', 'id', 'plan_id');
    }
    
    public function orderUser()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
