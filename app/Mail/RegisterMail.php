<?php

namespace App\Mail;

use Spatie\MailTemplates\TemplateMailable;

class RegisterMail extends TemplateMailable
{
    public $name;
    public $email;

    public function __construct($request)
    {
        $this->name     = $request->name;
        $this->email    = $request->email;
    }

    public function getHtmlLayout(): string
    {
        return view('mails.layout')->render();
    }
}
