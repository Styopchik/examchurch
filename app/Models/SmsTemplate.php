<?php

namespace App\Models;

use App\Facades\UtilityFacades;
use Twilio\Rest\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'event',
        'template',
        'variables',
    ];

    public function send($number, $data)
    {
        $message = __($this->template, $data);
        return $this->__sendSMS($number, $message);
    }

    private function __sendSMS($number, $message)
    {
        $user = \Auth::user();
        if ($user->type == 'Super Admin') {
            $createdBy          = $user->id;
        } else {
            $createdBy          = $user->created_by;
        }
        try {
            $sid                = UtilityFacades::keysettings('TWILIO_SID', $createdBy);
            $token              = UtilityFacades::keysettings('TWILIO_AUTH_TOKEN', $createdBy);
            $twilioNumber       = UtilityFacades::keysettings('TWILIO_NUMBER', $createdBy);
            $client             = new Client($sid, $token);
            $client->messages->create($number, [
                'from' => $twilioNumber,
                'body' => $message
            ]);
            return ['is_success' => true];
        } catch (\Exception $e) {
            return ['is_success' => false, 'message' => $e->getMessage()];
        }
    }
}
