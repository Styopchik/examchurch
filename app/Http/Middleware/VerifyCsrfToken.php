<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        '/fillcallback*',
        '/paytm-callback*',
        'forms/fill/*',
        '/paytm/callback*',
        '/payment/callback*',
        '/paypayment/callback*',
        '/paypayment/paytm/callback*',
        '/payumoney/success*',
        '/payumoney/failure*',
        'paytab-success/*',
        'plan/paytab-success/*'
    ];
}
