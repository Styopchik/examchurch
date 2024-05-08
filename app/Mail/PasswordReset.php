<?php

namespace App\Mail;

use Spatie\MailTemplates\TemplateMailable;

class PasswordReset extends TemplateMailable
{
    public $url;

    public function __construct($url)
    {
        $this->url = $url;
    }
    
    public function getHtmlLayout(): string
    {
        return view('mails.layout')->render();
    }
}
