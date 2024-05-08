<?php

namespace App\Mail;

use Spatie\MailTemplates\TemplateMailable;

class ApproveOfflineMail extends TemplateMailable
{
    public $name;
    public $planName;
    public $amount;
    public $expireDate;

    public function __construct( $plan,$user)
    {
        $this->name         = $user->name;
        $this->planName     = $plan->name;
        $this->amount       = $plan->price;
        $this->expireDate   = $user->plan_expired_date;
    }

    public function getHtmlLayout(): string
    {
        return view('mails.layout')->render();
    }
}
