<?php

namespace App\Mail;

use App\Models\RequestUser;
use Spatie\MailTemplates\TemplateMailable;

class DisapprovedMail extends TemplateMailable
{
    public $name;
    public $email;
    public $reason;

    public function __construct(RequestUser $details)
    {
        $this->name     = $details->name;
        $this->email    = $details->email;
        $this->reason   = $details->disapprove_reason;
    }

    public function getHtmlLayout(): string
    {
        return view('mails.layout')->render();
    }
}
