<?php

namespace App\Models;

use App\Mail\FormSubmitEmail;
use BulkGate\Message\Connection;
use BulkGate\Sms\Message;
use BulkGate\Sms\Sender;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use mediaburst\ClockworkSMS\Clockwork;
use Twilio\Rest\Client;
use Vonage\Client as VonageClient;
use Vonage\Client\Credentials\Basic;
use Vonage\SMS\Message\SMS;

class Form extends Model
{
    use HasFactory;

    public $fillable = [
        'title', 'json', 'logo', 'success_msg', 'thanks_msg', 'email', 'amount', 'currency_symbol', 'theme', 'theme_color', 'theme_background_image',
        'currency_name', 'payment_status', 'created_by', 'payment_type', 'bccemail', 'ccemail', 'allow_comments','category_id',
        'allow_share_section', 'assign_type', 'set_end_date', 'set_end_date_time','limit','limit_status','form_fill_edit_lock'
    ];

    public function getFormArray()
    {
        return json_decode($this->json);
    }

    public function Form()
    {
        return $this->hasMany('App\Models\FormValue', 'form_id', 'id');
    }

    public function User()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function Roles()
    {
        return $this->belongsToMany('Spatie\Permission\Models\Role', 'user_forms', 'form_id', 'role_id');
    }

    public function assignFormRoles($role_ids)
    {
        $roles = $this->Roles->pluck('name', 'id')->toArray();
        if ($role_ids) {
            foreach ($role_ids as $id) {
                if (!array_key_exists($id, $roles)) {
                    UserForm::create(['form_id' => $this->id, 'role_id' => $id]);
                } else {
                    unset($roles[$id]);
                }
            }
        }
        if ($roles) {
            foreach ($roles as $id => $name) {
                UserForm::where(['form_id' => $this->id, 'role_id' => $id])->delete();
            }
        }
    }

    public function commmant()
    {
        return $this->hasMany(FormComments::class, 'form_id', 'id');
    }

    //assign form user
    public function assignedusers()
    {
        return $this->belongsToMany(User::class, 'assign_forms_users', 'form_id', 'user_id');
    }

    public function assignUser($users_ids)
    {
        $form_users = $this->assignedusers->pluck('name', 'id')->toArray();
        if ($users_ids) {
            foreach ($users_ids as $id) {
                if (!array_key_exists($id, $form_users)) {
                    AssignFormsUsers::create(['form_id' => $this->id, 'user_id' => $id]);
                } else {
                    unset($form_users[$id]);
                }
            }
        }
        if ($form_users) {
            foreach ($form_users as $id => $name) {
                AssignFormsUsers::where(['form_id' => $this->id, 'user_id' => $id])->delete();
            }
        }
    }

    //assign form roles
    public function assignedroles()
    {
        return $this->belongsToMany('Spatie\Permission\Models\Role', 'assign_forms_roles', 'form_id', 'role_id');
    }

    public function assignRole($usersIds)
    {
        $formRoles = $this->assignedroles->pluck('name', 'id')->toArray();
        if ($usersIds) {
            foreach ($usersIds as $id) {
                if (!array_key_exists($id, $formRoles)) {
                    AssignFormsRoles::create(['form_id' => $this->id, 'role_id' => $id]);
                } else {
                    unset($formRoles[$id]);
                }
            }
        }
        if ($formRoles) {
            foreach ($formRoles as $id => $name) {
                AssignFormsRoles::where(['form_id' => $this->id, 'role_id' => $id])->delete();
            }
        }
    }

    public static function integrationFormData($form, $formValue)
    {
        $appName    = env('APP_NAME');
        $formTitle  = $form->title;
        //slack integration
        $formSlackSetting           = FormIntegrationSetting::where('key', 'slack_integration')->where('form_id', $form->id)->where('status', 1)->first();
        if ($formSlackSetting) {
            if ($formSlackSetting->json) {
                $slackFieldJsons    = json_decode($formSlackSetting->field_json, true);
                $slackJsons         = json_decode($formSlackSetting->json, true);
                foreach ($slackJsons as $slackJsonkey => $slackJson) {
                    if ($slackJson['slack_webhook_url']) {
                        $slackData = [];
                        $slackData['blocks'] = [];
                        $slackData['blocks'][] = [
                            'type' => 'header',
                            'text' => [
                                'type' => 'plain_text',
                                'text' => "[$appName]\n\n$formTitle",
                                'emoji' => true
                            ]
                        ];
                        $formValueJsons = json_decode($formValue->json);
                        foreach ($formValueJsons as $formValueJson) {
                            foreach ($formValueJson as $formValue) {
                                foreach ($slackFieldJsons as $slackFieldKey => $slackFieldJson) {
                                    if ($slackFieldKey == $slackJsonkey) {
                                        $slackArr = explode(',', $slackFieldJson);
                                        if ($formValue->type == 'checkbox-group' || $formValue->type == 'radio-group' || $formValue->type == 'select') {
                                            if (in_array($formValue->name, $slackArr)) {
                                                foreach ($formValue->values as $Value) {
                                                    if (property_exists($Value, 'selected') && $Value->selected == 1) {
                                                        $val = $Value->label;
                                                        break; // Exit the loop early if a condition is not met
                                                    } else {
                                                        $val = 'null';
                                                    }
                                                }

                                                $slackData['blocks'][] = [
                                                    'type' => 'section',
                                                    'fields' => [
                                                        [
                                                            'type' => 'mrkdwn',
                                                            'text' => "*$formValue->label:*"
                                                        ],
                                                        [
                                                            'type' => 'mrkdwn',
                                                            'text' => $val
                                                        ]
                                                    ]
                                                ];
                                            }
                                        } elseif ($formValue->type == 'repeater') {
                                            $imageChoiceJsons = json_decode($formValue->value);
                                            foreach ($imageChoiceJsons as $imageChoiceJson) {
                                                if (isset($imageChoiceJson->selected) && $imageChoiceJson->selected == 1) {
                                                    $slackData['blocks'][] = [
                                                        'type' => 'section',
                                                        'text' => [
                                                            'type' => 'mrkdwn',
                                                            'text' => "*$imageChoiceJsons->label:*"
                                                        ],
                                                        'accessory' => [
                                                            'type' => 'image',
                                                            'image_url' => asset(Storage::url($imageChoiceJsons->image)),
                                                            'alt_text' => $imageChoiceJsons->value
                                                        ]
                                                    ];
                                                }
                                            }
                                        } elseif ($formValue->type != 'button' && $formValue->type != 'file'  && $formValue->type != 'header' && $formValue->type != 'hidden' && $formValue->type != 'paragraph' && $formValue->type != 'SignaturePad' && $formValue->type != 'video' && $formValue->type != 'selfie' && $formValue->type != 'break' && $formValue->type != 'location') {
                                            if (in_array($formValue->name, $slackArr)) {
                                                $val = (property_exists($formValue, 'value')) ? $formValue->value : 'null';
                                                $slackData['blocks'][] = [
                                                    'type' => 'section',
                                                    'fields' => [
                                                        [
                                                            'type' => 'mrkdwn',
                                                            'text' => "*$formValue->label:*"
                                                        ],
                                                        [
                                                            'type' => 'mrkdwn',
                                                            'text' => $val
                                                        ]
                                                    ]
                                                ];
                                            }
                                        } elseif ($formValue->type == 'file' || $formValue->type == 'SignaturePad') {
                                            if (in_array($formValue->name, $slackArr)) {
                                                if (property_exists($formValue, 'multiple') && $formValue->multiple) {
                                                    if (property_exists($formValue, 'value')) {
                                                        $files = $formValue->value;
                                                        $accessoryImages = [];
                                                        foreach ($files as $file) {
                                                            $accessoryImages[] = [
                                                                'type' => 'image',
                                                                'image_url' => asset(Storage::url($file)),
                                                                'alt_text' => $formValue->name
                                                            ];
                                                        }
                                                        foreach ($accessoryImages as $image) {
                                                            $slackData['blocks'][] = [
                                                                'type' => 'section',
                                                                'text' => [
                                                                    'type' => 'mrkdwn',
                                                                    'text' => "*$formValue->label:*"
                                                                ],
                                                                'accessory' => $image
                                                            ];
                                                        }
                                                    } else {
                                                        $slackData['blocks'][] = [
                                                            'type' => 'section',
                                                            'fields' => [
                                                                [
                                                                    'type' => 'mrkdwn',
                                                                    'text' => "*$formValue->label:*"
                                                                ],
                                                                [
                                                                    'type' => 'mrkdwn',
                                                                    'text' => 'null'
                                                                ]
                                                            ]
                                                        ];
                                                    }
                                                } else {
                                                    if (property_exists($formValue, 'value')) {
                                                        if (is_array($formValue->value)) {
                                                            $files = $formValue->value;
                                                            $accessoryImages = [];
                                                            foreach ($files as $file) {
                                                                $accessoryImages[] = [
                                                                    'type' => 'image',
                                                                    'image_url' => asset(Storage::url($file)),
                                                                    'alt_text' => $formValue->name
                                                                ];
                                                            }
                                                            foreach ($accessoryImages as $image) {
                                                                $slackData['blocks'][] = [
                                                                    'type' => 'section',
                                                                    'text' => [
                                                                        'type' => 'mrkdwn',
                                                                        'text' => "*$formValue->label:*"
                                                                    ],
                                                                    'accessory' => $image
                                                                ];
                                                            }
                                                        } else {
                                                            $slackData['blocks'][] = [
                                                                'type' => 'section',
                                                                'text' => [
                                                                    'type' => 'mrkdwn',
                                                                    'text' => "*$formValue->label:*"
                                                                ],
                                                                'accessory' => [
                                                                    'type' => 'image',
                                                                    'image_url' => asset(Storage::url($formValue->value)),
                                                                    'alt_text' => $formValue->name
                                                                ]
                                                            ];
                                                        }
                                                    } else {
                                                        $slackData['blocks'][] = [
                                                            'type' => 'section',
                                                            'fields' => [
                                                                [
                                                                    'type' => 'mrkdwn',
                                                                    'text' => "*$formValue->label:*"
                                                                ],
                                                                [
                                                                    'type' => 'mrkdwn',
                                                                    'text' => 'null'
                                                                ]
                                                            ]
                                                        ];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        try {
                            $response = Http::post($slackJson['slack_webhook_url'], [
                                'text'   => "[$appName]",
                                'blocks' => $slackData['blocks']
                            ]);
                        } catch (\Exception $e) {
                        }
                    }
                }
            }
        }

        //telegram integration
        $formTelegramSetting = FormIntegrationSetting::where('key', 'telegram_integration')->where('form_id', $form->id)->where('status', 1)->first();
        if ($formTelegramSetting) {
            if ($formTelegramSetting->json) {
                $telegramFieldJsons = json_decode($formTelegramSetting->field_json, true);
                $telegramJsons      = json_decode($formTelegramSetting->json, true);
                foreach ($telegramJsons as $telegramJsonKey => $telegramJson) {
                    if ($telegramJson['telegram_access_token'] && $telegramJson['telegram_chat_id']) {
                        $telTable   = '';
                        $telTable   .= "[$appName]\n\n$formTitle\n\n";
                        $telTable   .= "<b>Field Label</b> | <b>Value</b>\n";
                        $telTable   .= "| --- | --- |\n";
                        $formValueJsons = json_decode($formValue->json);
                        foreach ($formValueJsons as $formValueJson) {
                            foreach ($formValueJson as $formValue) {
                                foreach ($telegramFieldJsons as $telegramFieldkey => $telegramFieldJson) {
                                    if ($telegramFieldkey == $telegramJsonKey) {
                                        $telegramarr        = explode(',', $telegramFieldJson);
                                        if ($formValue->type == 'checkbox-group' || $formValue->type == 'radio-group' || $formValue->type == 'select') {
                                            if (in_array($formValue->name, $telegramarr)) {
                                                foreach ($formValue->values as $Value) {
                                                    if (property_exists($Value, 'selected') && $Value->selected == 1) {
                                                        $val = $Value->label;
                                                        break; // Exit the loop early if a condition is not met
                                                    } else {
                                                        $val = 'null';
                                                    }
                                                }
                                                $telTable   .= "| {$formValue->label} | {$Value->label} |\n";
                                            }
                                        } elseif ($formValue->type != 'button' && $formValue->type != 'file'  && $formValue->type != 'header' && $formValue->type != 'hidden' && $formValue->type != 'paragraph' && $formValue->type != 'repeater' && $formValue->type != 'SignaturePad' && $formValue->type != 'video' && $formValue->type != 'selfie' && $formValue->type != 'break' && $formValue->type != 'location') {
                                            if (in_array($formValue->name, $telegramarr)) {
                                                $val        = (property_exists($formValue, 'value')) ? $formValue->value : 'null';
                                                $telTable   .= "| {$formValue->label} | {$val} |\n";
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        try {
                            $telegramMessage    = "<pre>{$telTable}</pre>";
                            $telegramBot        = $telegramJson['telegram_access_token'];
                            $telegramChatId     = $telegramJson['telegram_chat_id'];
                            $response           = Http::post("https://api.telegram.org/bot{$telegramBot}/sendMessage", [
                                'chat_id'       => $telegramChatId,
                                'text'          => $telegramMessage,
                                'parse_mode'    => 'HTML',
                            ]);
                            if ($response->failed()) {
                            }
                            $responseData = $response->json();
                        } catch (\Exception $e) {
                        }
                    }
                }
            }
        }

        //mailgun integration
        $formMailgunSetting         = FormIntegrationSetting::where('key', 'mailgun_integration')->where('form_id', $form->id)->where('status', 1)->first();
        $formVale                   = [];
        if ($formMailgunSetting) {
            if ($formMailgunSetting->json) {
                $mailgunFieldJsons  = json_decode($formMailgunSetting->field_json, true);
                $mailgunJsons       = json_decode($formMailgunSetting->json, true);
                foreach ($mailgunJsons as $mailgunJsonKey => $mailgunJson) {
                    if ($mailgunJson['mailgun_email'] && $mailgunJson['mailgun_domain'] && $mailgunJson['mailgun_secret'] && $mailgunJson['mailgun_mail_from_address']) {
                        $formValueJsons = json_decode($formValue->json);
                        foreach ($formValueJsons as $formValueJsonkgun => $formValueJson) {
                            foreach ($formValueJson as $formValueJsonk1gun => $formValue) {
                                foreach ($mailgunFieldJsons as $mailgunFieldkey => $mailgunFieldJson) {
                                    if ($mailgunFieldkey == $mailgunJsonKey) {
                                        $mailgunarr = explode(',', $mailgunFieldJson);
                                        if ($formValue->type == 'checkbox-group' || $formValue->type == 'radio-group' || $formValue->type == 'select') {
                                            if (in_array($formValue->name, $mailgunarr)) {
                                                $formVale[$formValueJsonkgun][$formValueJsonk1gun] = $formValue;
                                            }
                                        } elseif ($formValue->type != 'button' && $formValue->type != 'file'  && $formValue->type != 'header' && $formValue->type != 'repeater' && $formValue->type != 'hidden' && $formValue->type != 'paragraph' && $formValue->type != 'SignaturePad' && $formValue->type != 'video' && $formValue->type != 'selfie' && $formValue->type != 'break' && $formValue->type != 'location') {
                                            if (in_array($formValue->name, $mailgunarr)) {
                                                $formVale[$formValueJsonkgun][$formValueJsonk1gun] = $formValue;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        config([
                            'mail.default'              => 'mailgun',
                            'services.mailgun.domain'   => $mailgunJson['mailgun_domain'],
                            'services.mailgun.secret'   => $mailgunJson['mailgun_secret'],
                            'mail.from.address'         => $mailgunJson['mailgun_mail_from_address'],
                            'mail.from.name'            => $appName,
                        ]);
                        try {
                            Mail::to($mailgunJson['mailgun_email'])->send(new FormSubmitEmail($formValue, $formVale));
                        } catch (\Exception $e) {
                        }
                    }
                }
            }
        }

        // bulkgate integration
        $formBulkgateSetting            = FormIntegrationSetting::where('key', 'bulkgate_integration')->where('form_id', $form->id)->where('status', 1)->first();
        if ($formBulkgateSetting) {
            if ($formBulkgateSetting->json) {
                $bulkgateFieldJsons     = json_decode($formBulkgateSetting->field_json, true);
                $bulkgateJsons          = json_decode($formBulkgateSetting->json, true);
                foreach ($bulkgateJsons as $bulkgateJsonkey => $bulkgateJson) {
                    if ($bulkgateJson['bulkgate_number'] && $bulkgateJson['bulkgate_token'] && $bulkgateJson['bulkgate_app_id']) {
                        $telTable       = '';
                        $telTable       .= "[$appName]\n\n$formTitle\n\n";
                        $formValueJsons = json_decode($formValue->json);
                        foreach ($formValueJsons as $formValueJson) {
                            foreach ($formValueJson as $formValue) {
                                foreach ($bulkgateFieldJsons as $bulkgateFieldKey => $bulkgateFieldJson) {
                                    if ($bulkgateFieldKey == $bulkgateJsonkey) {
                                        $bulkgateArr = explode(',', $bulkgateFieldJson);
                                        if ($formValue->type == 'checkbox-group' || $formValue->type == 'radio-group' || $formValue->type == 'select') {
                                            if (in_array($formValue->name, $bulkgateArr)) {
                                                foreach ($formValue->values as $Value) {
                                                    if (property_exists($Value, 'selected') && $Value->selected == 1) {
                                                        $val = $Value->label;
                                                        break; // Exit the loop early if a condition is not met
                                                    } else {
                                                        $val = 'null';
                                                    }
                                                    $telTable .= "\n" . str_pad($formValue->label, 20, " ") . ": " . $val;
                                                }
                                            }
                                        } elseif ($formValue->type != 'button' &&  $formValue->type != 'header' && $formValue->type != 'repeater' && $formValue->type != 'hidden' && $formValue->type != 'paragraph' && $formValue->type != 'video' && $formValue->type != 'selfie' && $formValue->type != 'break' && $formValue->type != 'location') {
                                            if (in_array($formValue->name, $bulkgateArr)) {
                                                $val        = (property_exists($formValue, 'value')) ? $formValue->value : null;
                                                $telTable   .= "\n" . str_pad($formValue->label, 20, " ") . ": " . $val;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $connection = new Connection($bulkgateJson['bulkgate_app_id'], $bulkgateJson['bulkgate_token']);
                        $sender = new Sender($connection);
                        $message = new Message($bulkgateJson['bulkgate_number'], $telTable);
                        $sender->send($message);
                    }
                }
            }
        }

        // nexmo integration
        $formNexmoSetting = FormIntegrationSetting::where('key', 'nexmo_integration')->where('form_id', $form->id)->where('status', 1)->first();
        if ($formNexmoSetting) {
            if ($formNexmoSetting->json) {
                $nexmoFieldJsons = json_decode($formNexmoSetting->field_json, true);
                $nexmoJsons      = json_decode($formNexmoSetting->json, true);
                foreach ($nexmoJsons as $nexmoJsonKey => $nexmoJson) {
                    if ($nexmoJson['nexmo_number'] && $nexmoJson['nexmo_key'] && $nexmoJson['nexmo_secret']) {
                        $telTable       = '';
                        $telTable       .= "[$appName]\n\n$formTitle\n\n";
                        $formValueJsons = json_decode($formValue->json);
                        foreach ($formValueJsons as $formValueJson) {
                            foreach ($formValueJson as $formValue) {
                                foreach ($nexmoFieldJsons as $nexmoFieldkey => $nexmoFieldJson) {
                                    if ($nexmoFieldkey == $nexmoJsonKey) {
                                        $nexmoarr = explode(',', $nexmoFieldJson);
                                        if ($formValue->type == 'checkbox-group' || $formValue->type == 'radio-group' || $formValue->type == 'select') {
                                            if (in_array($formValue->name, $nexmoarr)) {
                                                foreach ($formValue->values as $Value) {
                                                    if (property_exists($Value, 'selected') && $Value->selected == 1) {
                                                        $val = $Value->label;
                                                        break; // Exit the loop early if a condition is not met
                                                    } else {
                                                        $val = 'null';
                                                    }
                                                }
                                                $telTable .= "\n" . $formValue->label . ": " . $val;
                                            }
                                        } elseif ($formValue->type != 'button' && $formValue->type != 'repeater' && $formValue->type != 'header' && $formValue->type != 'hidden' && $formValue->type != 'paragraph' && $formValue->type != 'video' && $formValue->type != 'selfie' && $formValue->type != 'break' && $formValue->type != 'location') {
                                            if (in_array($formValue->name, $nexmoarr)) {
                                                $val = (property_exists($formValue, 'value')) ? $formValue->value : null;
                                                $telTable .= "\n" . $formValue->label . ": " . $val;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $response           = Http::asForm()->post('https://rest.nexmo.com/sms/json/', [
                            'api_key'       => $nexmoJson['nexmo_key'],
                            'api_secret'    => $nexmoJson['nexmo_secret'],
                            'from'          => $appName,
                            'text'          => $telTable,
                            'to'            => $nexmoJson['nexmo_number']
                        ]);
                    }
                }
            }
        }

        // fast2sms integration
        $formFast2SmsSetting            = FormIntegrationSetting::where('key', 'fast2sms_integration')->where('form_id', $form->id)->where('status', 1)->first();
        if ($formFast2SmsSetting) {
            if ($formFast2SmsSetting->json) {
                $fast2smsFieldJsons     = json_decode($formFast2SmsSetting->field_json, true);
                $fast2smsJsons          = json_decode($formFast2SmsSetting->json, true);
                foreach ($fast2smsJsons as $fast2smsJsonkey => $fast2smsJson) {
                    if ($fast2smsJson['fast2sms_number'] && $fast2smsJson['fast2sms_api_key']) {
                        $telTable       = '';
                        $telTable       .= "[$appName]\n\n$formTitle\n\n";
                        $formValueJsons = json_decode($formValue->json);
                        foreach ($formValueJsons as $formValueJson) {
                            foreach ($formValueJson as $formValue) {
                                foreach ($fast2smsFieldJsons as $fast2smsFieldKey => $fast2smsFieldJson) {
                                    if ($fast2smsFieldKey == $fast2smsJsonkey) {
                                        $fast2smsArr = explode(',', $fast2smsFieldJson);
                                        if ($formValue->type == 'checkbox-group' || $formValue->type == 'radio-group' || $formValue->type == 'select') {
                                            if (in_array($formValue->name, $fast2smsArr)) {
                                                foreach ($formValue->values as $Value) {
                                                    if (property_exists($Value, 'selected') && $Value->selected == 1) {
                                                        $val = $Value->label;
                                                        break; // Exit the loop early if a condition is not met
                                                    } else {
                                                        $val = 'null';
                                                    }
                                                }
                                                $telTable .= "\n" . $formValue->label . ": " . $val;
                                            }
                                        } elseif ($formValue->type != 'button' && $formValue->type != 'repeater' &&  $formValue->type != 'header' && $formValue->type != 'hidden' && $formValue->type != 'paragraph' && $formValue->type != 'video' && $formValue->type != 'selfie' && $formValue->type != 'break' && $formValue->type != 'location') {
                                            if (in_array($formValue->name, $fast2smsArr)) {
                                                $val = (property_exists($formValue, 'value')) ? $formValue->value : null;
                                                $telTable .= "\n" . $formValue->label . ": " . $val;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $fields = array(
                            "message"   => $telTable,
                            "language"  => "english",
                            "route"     => "q",
                            "numbers"   => $fast2smsJson['fast2sms_number'],
                        );

                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => "https://www.fast2sms.com/dev/bulkV2",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 30,
                            CURLOPT_SSL_VERIFYHOST => 0,
                            CURLOPT_SSL_VERIFYPEER => 0,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "POST",
                            CURLOPT_POSTFIELDS => json_encode($fields),
                            CURLOPT_HTTPHEADER => array(
                                "authorization: " . $fast2smsJson['fast2sms_api_key'],
                                "accept: */*",
                                "cache-control: no-cache",
                                "content-type: application/json"
                            ),
                        ));

                        $response = curl_exec($curl);
                    }
                }
            }
        }

        // vonage integration
        $formVonageSetting = FormIntegrationSetting::where('key', 'vonage_integration')->where('form_id', $form->id)->where('status', 1)->first();
        if ($formVonageSetting) {
            if ($formVonageSetting->json) {
                $vonageFieldJsons   = json_decode($formVonageSetting->field_json, true);
                $vonageJsons        = json_decode($formVonageSetting->json, true);
                foreach ($vonageJsons as $vonageJsonKey => $vonageJson) {
                    if ($vonageJson['vonage_number'] && $vonageJson['vonage_key'] && $vonageJson['vonage_secret']) {
                        $telTable = '';
                        $telTable .= "[$appName]\n\n$formTitle\n\n";
                        $formValueJsons = json_decode($formValue->json);
                        foreach ($formValueJsons as $formValueJson) {
                            foreach ($formValueJson as $formValue) {
                                foreach ($vonageFieldJsons as $vonageFieldKey => $vonageFieldJson) {
                                    if ($vonageFieldKey == $vonageJsonKey) {
                                        $vonageArr = explode(',', $vonageFieldJson);
                                        if ($formValue->type == 'checkbox-group' || $formValue->type == 'radio-group' || $formValue->type == 'select') {
                                            if (in_array($formValue->name, $vonageArr)) {
                                                foreach ($formValue->values as $Value) {
                                                    if (property_exists($Value, 'selected') && $Value->selected == 1) {
                                                        $val = $Value->label;
                                                        break; // Exit the loop early if a condition is not met
                                                    } else {
                                                        $val = 'null';
                                                    }
                                                }
                                                $telTable .= "\n" . $formValue->label . ": " . $val;
                                            }
                                        } elseif ($formValue->type != 'button' && $formValue->type != 'repeater' &&  $formValue->type != 'header' && $formValue->type != 'hidden' && $formValue->type != 'paragraph' && $formValue->type != 'video' && $formValue->type != 'selfie' && $formValue->type != 'break' && $formValue->type != 'location') {
                                            if (in_array($formValue->name, $vonageArr)) {
                                                $val = (property_exists($formValue, 'value')) ? $formValue->value : null;
                                                $telTable .= "\n" . $formValue->label . ": " . $val;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $basic      = new  Basic($vonageJson['vonage_key'], $vonageJson['vonage_secret']);
                        $client     = new VonageClient($basic);
                        $response   = $client->sms()->send(
                            new SMS($vonageJson['vonage_number'], "Vonage APIs", $telTable)
                        );
                    }
                }
            }
        }

        //sendgrid integration
        $formSendGridSetting = FormIntegrationSetting::where('key', 'sendgrid_integration')->where('form_id', $form->id)->where('status', 1)->first();
        $formVale = [];
        if ($formSendGridSetting) {
            if ($formSendGridSetting->json) {
                $sendgridFieldJsons = json_decode($formSendGridSetting->field_json, true);
                $sendgridJsons = json_decode($formSendGridSetting->json, true);
                foreach ($sendgridJsons as $sendgridJsonKey => $sendgridJson) {
                    if ($sendgridJson['sendgrid_email'] && $sendgridJson['sendgrid_host'] && $sendgridJson['sendgrid_port'] && $sendgridJson['sendgrid_username'] && $sendgridJson['sendgrid_password'] && $sendgridJson['sendgrid_encryption'] && $sendgridJson['sendgrid_from_address'] && $sendgridJson['sendgrid_from_name']) {
                        $formValueJsons = json_decode($formValue->json);
                        foreach ($formValueJsons as $formValueJsonkGrid => $formValueJson) {
                            foreach ($formValueJson as $formValueJsonk1Grid => $formValue) {
                                foreach ($sendgridFieldJsons as $sendgridFieldkey => $sendgridFieldJson) {
                                    if ($sendgridFieldkey == $sendgridJsonKey) {
                                        $sendgridarr = explode(',', $sendgridFieldJson);
                                        if ($formValue->type == 'checkbox-group' || $formValue->type == 'radio-group' || $formValue->type == 'select') {
                                            if (in_array($formValue->name, $sendgridarr)) {
                                                $formVale[$formValueJsonkGrid][$formValueJsonk1Grid] = $formValue;
                                            }
                                        } elseif ($formValue->type != 'button' && $formValue->type != 'file'  && $formValue->type != 'header' && $formValue->type != 'hidden' && $formValue->type != 'paragraph' && $formValue->type != 'SignaturePad' && $formValue->type != 'video' && $formValue->type != 'selfie' && $formValue->type != 'break' && $formValue->type != 'location') {
                                            if (in_array($formValue->name, $sendgridarr)) {
                                                $formVale[$formValueJsonkGrid][$formValueJsonk1Grid] = $formValue;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        config([
                            'mail.default'                 => 'sendgrid',
                            'mail.mailers.smtp.host'       => $sendgridJson['sendgrid_host'],
                            'mail.mailers.smtp.port'       => $sendgridJson['sendgrid_port'],
                            'mail.mailers.smtp.encryption' => $sendgridJson['sendgrid_encryption'],
                            'mail.mailers.smtp.username'   => $sendgridJson['sendgrid_username'],
                            'services.sendgrid.api_key'    => $sendgridJson['sendgrid_password'],
                            'mail.from.address'            => $sendgridJson['sendgrid_from_address'],
                            'mail.from.name'               => $sendgridJson['sendgrid_from_name'],
                        ]);
                        try {
                            Mail::to($sendgridJson['sendgrid_email'])->send(new FormSubmitEmail($formValue, $formVale));
                        } catch (\Exception $e) {
                        }
                    }
                }
            }
        }

        // twilio integration
        $formTwilioSetting = FormIntegrationSetting::where('key', 'twilio_integration')->where('form_id', $form->id)->where('status', 1)->first();
        if ($formTwilioSetting) {
            if ($formTwilioSetting->json) {
                $twilioFieldJsons = json_decode($formTwilioSetting->field_json, true);
                $twilioJsons = json_decode($formTwilioSetting->json, true);
                foreach ($twilioJsons as $twilioJsonKey => $twilioJson) {
                    if ($twilioJson['twilio_mobile_number'] && $twilioJson['twilio_sid'] && $twilioJson['twilio_auth_token'] && $twilioJson['twilio_number']) {
                        $telTable = '';
                        $telTable .= "\n[$appName]\n\n$formTitle\n\n";
                        $formValueJsons = json_decode($formValue->json);
                        foreach ($formValueJsons as $formValueJson) {
                            foreach ($formValueJson as $formValue) {
                                foreach ($twilioFieldJsons as $twilioFieldKey => $twilioFieldJson) {
                                    if ($twilioFieldKey == $twilioJsonKey) {
                                        $twilioArr = explode(',', $twilioFieldJson);
                                        if ($formValue->type == 'checkbox-group' || $formValue->type == 'radio-group' || $formValue->type == 'select') {
                                            if (in_array($formValue->name, $twilioArr)) {
                                                foreach ($formValue->values as $Value) {
                                                    if (property_exists($Value, 'selected') && $Value->selected == 1) {
                                                        $val = $Value->label;
                                                        break; // Exit the loop early if a condition is not met
                                                    } else {
                                                        $val = 'null';
                                                    }
                                                }
                                                $telTable .= "\n" . str_pad($formValue->label, 20, " ") . ": " . $val;
                                            }
                                        } elseif ($formValue->type != 'button' && $formValue->type != 'repeater' &&  $formValue->type != 'header' && $formValue->type != 'hidden' && $formValue->type != 'paragraph' && $formValue->type != 'video' && $formValue->type != 'selfie' && $formValue->type != 'break' && $formValue->type != 'location') {
                                            if (in_array($formValue->name, $twilioArr)) {
                                                $val = (property_exists($formValue, 'value')) ? $formValue->value : null;
                                                $telTable .= "\n" . str_pad($formValue->label, 20, " ") . ": " . $val;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        try {
                            $client = new Client($twilioJson['twilio_sid'], $twilioJson['twilio_auth_token']);
                            // Use the Client to make requests to the Twilio REST API
                            $client->messages->create(
                                // The number you'd like to send the message to
                                '+' . $twilioJson['twilio_mobile_number'],
                                [
                                    // A Twilio phone number you purchased at https://console.twilio.com
                                    'from' => $twilioJson['twilio_number'],
                                    // The body of the text message you'd like to send
                                    'body' => $telTable
                                ]
                            );
                        } catch (Exception $e) {
                        }
                    }
                }
            }
        }

        // textlocal integration
        $formTextLocalSetting = FormIntegrationSetting::where('key', 'textlocal_integration')->where('form_id', $form->id)->where('status', 1)->first();
        if ($formTextLocalSetting) {
            if ($formTextLocalSetting->json) {
                $textlocalFieldJsons    = json_decode($formTextLocalSetting->field_json, true);
                $textlocalJsons         = json_decode($formTextLocalSetting->json, true);
                foreach ($textlocalJsons as $textlocalJsonKey => $textlocalJson) {
                    if ($textlocalJson['textlocal_number'] && $textlocalJson['textlocal_api_key']) {
                        $telTable       = '';
                        $telTable       .= "[$appName]\n\n$formTitle\n\n";
                        $formValueJsons = json_decode($formValue->json);
                        foreach ($formValueJsons as $formValueJson) {
                            foreach ($formValueJson as $formValue) {
                                foreach ($textlocalFieldJsons as $textlocalFieldKey => $textlocalFieldJson) {
                                    if ($textlocalFieldKey == $textlocalJsonKey) {
                                        $textlocalArr = explode(',', $textlocalFieldJson);
                                        if ($formValue->type == 'checkbox-group' || $formValue->type == 'radio-group' || $formValue->type == 'select') {
                                            if (in_array($formValue->name, $textlocalArr)) {
                                                foreach ($formValue->values as $Value) {
                                                    if (property_exists($Value, 'selected') && $Value->selected == 1) {
                                                        $val = $Value->label;
                                                        break; // Exit the loop early if a condition is not met
                                                    } else {
                                                        $val = 'null';
                                                    }
                                                }
                                                $telTable .= "\n" . str_pad($formValue->label, 20, " ") . ": " . $val;
                                            }
                                        } elseif ($formValue->type != 'button' && $formValue->type != 'repeater' &&  $formValue->type != 'header' && $formValue->type != 'hidden' && $formValue->type != 'paragraph' && $formValue->type != 'video' && $formValue->type != 'selfie' && $formValue->type != 'break' && $formValue->type != 'location') {
                                            if (in_array($formValue->name, $textlocalArr)) {
                                                $val        = (property_exists($formValue, 'value')) ? $formValue->value : null;
                                                $telTable   .= "\n" . str_pad($formValue->label, 20, " ") . ": " . $val;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        try {
                            $response = Http::asForm()->post('https://api.textlocal.in/send/', [
                                'form_params' => [
                                    'apikey'    => $textlocalJson['textlocal_api_key'],
                                    'sender'    => 'Prime Builder',
                                    'numbers'   => $textlocalJson['textlocal_number'],
                                    'message'   => $telTable,
                                ],
                            ]);
                            $responseData = $response->json();
                        } catch (\Exception $e) {
                        }
                    }
                }
            }
        }

        // messente integration
        $formMessenteSetting = FormIntegrationSetting::where('key', 'messente_integration')->where('form_id', $form->id)->where('status', 1)->first();
        if ($formMessenteSetting) {
            if ($formMessenteSetting->json) {
                $messenteFieldJsons = json_decode($formMessenteSetting->field_json, true);
                $messenteJsons = json_decode($formMessenteSetting->json, true);
                foreach ($messenteJsons as $messenteJsonKey => $messenteJson) {
                    if ($messenteJson['messente_number'] && $messenteJson['messente_api_username'] && $messenteJson['messente_api_password'] && $messenteJson['messente_sender']) {
                        $telTable = '';
                        $telTable .= "[$appName]\n\n$formTitle\n\n";
                        $formValueJsons = json_decode($formValue->json);
                        foreach ($formValueJsons as $formValueJson) {
                            foreach ($formValueJson as $formValue) {
                                foreach ($messenteFieldJsons as $messenteFieldKey => $messenteFieldJson) {
                                    if ($messenteFieldKey == $messenteJsonKey) {
                                        $messenteArr = explode(',', $messenteFieldJson);
                                        if ($formValue->type == 'checkbox-group' || $formValue->type == 'radio-group' || $formValue->type == 'select') {
                                            if (in_array($formValue->name, $messenteArr)) {
                                                foreach ($formValue->values as $Value) {
                                                    if (property_exists($Value, 'selected') && $Value->selected == 1) {
                                                        $val = $Value->label;
                                                        break; // Exit the loop early if a condition is not met
                                                    } else {
                                                        $val = 'null';
                                                    }
                                                }
                                                $telTable .= "\n" . $formValue->label . ": " . $val;
                                            }
                                        } elseif ($formValue->type != 'button' && $formValue->type != 'repeater' &&  $formValue->type != 'header' && $formValue->type != 'hidden' && $formValue->type != 'paragraph' && $formValue->type != 'video' && $formValue->type != 'selfie' && $formValue->type != 'break' && $formValue->type != 'location') {
                                            if (in_array($formValue->name, $messenteArr)) {
                                                $val        = (property_exists($formValue, 'value')) ? $formValue->value : null;
                                                $telTable   .= "\n" . $formValue->label . ": " . $val;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        try {
                            $messagePayload = [
                                'to' => '+' . $messenteJson['messente_number'],
                                'messages' => [
                                    [
                                        'channel'   => 'sms',
                                        'sender'    => $messenteJson['messente_sender'],
                                        'text'      => $telTable,
                                    ],
                                ],
                            ];
                            $response = Http::withBasicAuth($messenteJson['messente_api_username'], $messenteJson['messente_api_password'])
                                ->withHeaders(['Content-Type' => 'application/json'])
                                ->post('https://api.messente.com/v1/omnimessage', $messagePayload);

                            $responseData = $response->json();
                        } catch (\Exception $e) {
                        }
                    }
                }
            }
        }

        // smsgateway integration
        $formSmsGatewaySetting = FormIntegrationSetting::where('key', 'smsgateway_integration')->where('form_id', $form->id)->where('status', 1)->first();
        if ($formSmsGatewaySetting) {
            if ($formSmsGatewaySetting->json) {
                $smsgatewayFieldJsons = json_decode($formSmsGatewaySetting->field_json, true);
                $smsgatewayJsons = json_decode($formSmsGatewaySetting->json, true);
                foreach ($smsgatewayJsons as $smsgatewayJsonkey => $smsgatewayJson) {
                    if ($smsgatewayJson['smsgateway_number'] && $smsgatewayJson['smsgateway_api_key'] && $smsgatewayJson['smsgateway_user_id'] && $smsgatewayJson['smsgateway_user_password'] && $smsgatewayJson['smsgateway_sender_id']) {
                        $telTable = '';
                        $telTable .= "[$appName]\n\n$formTitle\n\n";
                        $formValueJsons = json_decode($formValue->json);
                        foreach ($formValueJsons as $formValueJson) {
                            foreach ($formValueJson as $formValue) {
                                foreach ($smsgatewayFieldJsons as $smsgatewayFieldKey => $smsgatewayFieldJson) {
                                    if ($smsgatewayFieldKey == $smsgatewayJsonkey) {
                                        $smsgatewayArr = explode(',', $smsgatewayFieldJson);
                                        if ($formValue->type == 'checkbox-group' || $formValue->type == 'radio-group' || $formValue->type == 'select') {
                                            if (in_array($formValue->name, $smsgatewayArr)) {
                                                foreach ($formValue->values as $Value) {
                                                    if (property_exists($Value, 'selected') && $Value->selected == 1) {
                                                        $val = $Value->label;
                                                        break; // Exit the loop early if a condition is not met
                                                    } else {
                                                        $val = 'null';
                                                    }
                                                }
                                                $telTable .= "\n" . $formValue->label . ": " . $val;
                                            }
                                        } elseif ($formValue->type != 'button' && $formValue->type != 'repeater' && $formValue->type != 'header' && $formValue->type != 'hidden' && $formValue->type != 'paragraph' && $formValue->type != 'video' && $formValue->type != 'selfie' && $formValue->type != 'break' && $formValue->type != 'location') {
                                            if (in_array($formValue->name, $smsgatewayArr)) {
                                                $val = (property_exists($formValue, 'value')) ? $formValue->value : null;
                                                $telTable .= "\n" . $formValue->label . ": " . $val;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        try {
                            $response = Http::withHeaders([
                                'apikey'            => $smsgatewayJson['smsgateway_api_key'],
                                'cache-control'     => 'no-cache',
                                'content-type'      => 'application/x-www-form-urlencoded',
                            ])->post('https://www.smsgateway.center/SMSApi/rest/send', [
                                'userId'            => $smsgatewayJson['smsgateway_user_id'],
                                'password'          => $smsgatewayJson['smsgateway_user_password'],
                                'senderId'          => $smsgatewayJson['smsgateway_sender_id'],
                                'sendMethod'        => 'simpleMsg',
                                'msgType'           => 'text',
                                'mobile'            => $smsgatewayJson['smsgateway_number'],
                                'msg'               => $telTable,
                                'duplicateCheck'    => 'true',
                                'format'            => 'json',
                            ]);
                        } catch (\Exception $e) {
                        }
                    }
                }
            }
        }

        // clicktell integration
        $formClicktellSetting           = FormIntegrationSetting::where('key', 'clicktell_integration')->where('form_id', $form->id)->where('status', 1)->first();
        if ($formClicktellSetting) {
            if ($formClicktellSetting->json) {
                $clicktellFieldJsons    = json_decode($formClicktellSetting->field_json, true);
                $clicktellJsons         = json_decode($formClicktellSetting->json, true);
                foreach ($clicktellJsons as $clicktellJsonKey => $clicktellJson) {
                    if ($clicktellJson['clicktell_number'] && $clicktellJson['clicktell_api_key']) {
                        $telTable = '';
                        $telTable .= "[$appName]\n\n$formTitle\n\n";
                        $formValueJsons = json_decode($formValue->json);
                        foreach ($formValueJsons as $formValueJson) {
                            foreach ($formValueJson as $formValue) {
                                foreach ($clicktellFieldJsons as $clicktellFieldKey => $clicktellFieldJson) {
                                    if ($clicktellFieldKey == $clicktellJsonKey) {
                                        $clicktellArr = explode(',', $clicktellFieldJson);
                                        if ($formValue->type == 'checkbox-group' || $formValue->type == 'radio-group' || $formValue->type == 'select') {
                                            if (in_array($formValue->name, $clicktellArr)) {
                                                foreach ($formValue->values as $Value) {
                                                    if (property_exists($Value, 'selected') && $Value->selected == 1) {
                                                        $val = $Value->label;
                                                        break; // Exit the loop early if a condition is not met
                                                    } else {
                                                        $val = 'null';
                                                    }
                                                }
                                                $telTable .= "\n" . $formValue->label . ": " . $val;
                                            }
                                        } elseif ($formValue->type != 'button' && $formValue->type != 'repeater' &&  $formValue->type != 'header' && $formValue->type != 'hidden' && $formValue->type != 'paragraph' && $formValue->type != 'video' && $formValue->type != 'selfie' && $formValue->type != 'break' && $formValue->type != 'location') {
                                            if (in_array($formValue->name, $clicktellArr)) {
                                                $val        = (property_exists($formValue, 'value')) ? $formValue->value : null;
                                                $telTable   .= "\n" . $formValue->label . ": " . $val;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        try {
                            $headers = [
                                "Content-Type"  => "application/json",
                                "Accept"        => "application/json",
                                "Authorization" => $clicktellJson['clicktell_api_key'],
                            ];
                            $clickTellData = [
                                "messages" => [
                                    [
                                        "channel"   => "sms",
                                        "to"        => $clicktellJson['clicktell_number'],
                                        "content"   => $telTable,
                                    ],
                                ],
                            ];
                            $response = Http::withHeaders($headers)->post('https://platform.clickatell.com/v1/message', $clickTellData);
                        } catch (\Exception $e) {
                        }
                    }
                }
            }
        }

        // clockwork integration
        $formClockWorkSetting = FormIntegrationSetting::where('key', 'clockwork_integration')->where('form_id', $form->id)->where('status', 1)->first();
        if ($formClockWorkSetting) {
            if ($formClockWorkSetting->json) {
                $clockworkFieldJsons = json_decode($formClockWorkSetting->field_json, true);
                $clockworkJsons = json_decode($formClockWorkSetting->json, true);
                foreach ($clockworkJsons as $clockworkJsonKey => $clockworkJson) {
                    if ($clockworkJson['clockwork_number'] && $clockworkJson['clockwork_api_token']) {
                        $telTable = '';
                        $telTable .= "[$appName]\n\n$formTitle\n\n";
                        $formValueJsons = json_decode($formValue->json);
                        foreach ($formValueJsons as $formValueJson) {
                            foreach ($formValueJson as $formValue) {
                                foreach ($clockworkFieldJsons as $clockworkFieldKey => $clockworkFieldJson) {
                                    if ($clockworkFieldKey == $clockworkJsonKey) {
                                        $clockworkArr = explode(',', $clockworkFieldJson);
                                        if ($formValue->type == 'checkbox-group' || $formValue->type == 'radio-group' || $formValue->type == 'select') {
                                            if (in_array($formValue->name, $clockworkArr)) {
                                                foreach ($formValue->values as $Value) {
                                                    if (property_exists($Value, 'selected') && $Value->selected == 1) {
                                                        $val = $Value->label;
                                                        break; // Exit the loop early if a condition is not met
                                                    } else {
                                                        $val = 'null';
                                                    }
                                                }
                                                $telTable .= "\n" . $formValue->label . ": " . $Value->label;
                                            }
                                        } elseif ($formValue->type != 'button' && $formValue->type != 'repeater' &&  $formValue->type != 'header' && $formValue->type != 'hidden' && $formValue->type != 'paragraph' && $formValue->type != 'video' && $formValue->type != 'selfie' && $formValue->type != 'break' && $formValue->type != 'location') {
                                            if (in_array($formValue->name, $clockworkArr)) {
                                                $val = (property_exists($formValue, 'value')) ? $formValue->value : null;
                                                $telTable .= "\n" . $formValue->label . ": " . $val;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        try {
                            $clockwork = new Clockwork($clockworkJson['clockwork_api_token']);
                            $result = $clockwork->send([
                                'to' => $clockworkJson['clockwork_number'],
                                'message' => $telTable
                            ]);
                        } catch (\Exception $e) {
                        }
                    }
                }
            }
        }
    }
}
