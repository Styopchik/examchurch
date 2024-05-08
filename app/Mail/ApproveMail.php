<?php

namespace App\Mail;

use App\Models\RequestUser;
use Spatie\MailTemplates\TemplateMailable;

class ApproveMail extends TemplateMailable
{
    public $name;
    public $email;

    public function __construct(RequestUser $details)
    {
        $this->name     = $details->name;
        $this->email    = $details->email;
    }

    public function getHtmlLayout(): string
    {
        return view('mails.layout')->render();
    }
}
