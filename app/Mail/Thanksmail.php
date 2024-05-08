<?php

namespace App\Mail;

use App\Models\FormValue;
use Spatie\MailTemplates\TemplateMailable;

class Thanksmail extends TemplateMailable
{
    public $title;
    public $thanksMsg;
    public $image;

    public function __construct(FormValue $formValue)
    {
        $this->title        = $formValue->Form->title;
        if (!empty($formValue->Form->logo)) {
        $this->image        = asset('storage/app/' . $formValue->Form->logo);
        }
        $this->thanksMsg    = strip_tags($formValue->Form->thanks_msg);
    }

    public function getHtmlLayout(): string
    {
        return view('mails.layout')->render();
    }
}
