<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FormSubmitEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $formValue;
    protected $formValueArray;

    public function __construct($formValue, $formValueArray)
    {
        $this->formValue        = $formValue;
        $this->formValueArray   = $formValueArray;
    }

    public function build()
    {
        return $this->markdown('emails.form-submit')->with(['formValue' => $this->formValue, 'formValueArray' => $this->formValueArray])->subject('New survey Submited - ' . $this->formValue->Form->title);
    }
}
