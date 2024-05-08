<?php

namespace Database\Seeders;

use App\Facades\UtilityFacades;
use App\Models\Module;
use App\Models\NotificationsSetting;
use App\Models\Plan;
use App\Models\settings;
use App\Models\SmsTemplate;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\MailTemplates\Models\MailTemplate;

class UserSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'user'                              => ['manage', 'create', 'edit', 'delete', 'impersonate', 'plan-upgrade', 'phone-verified', 'email-verified'],
            'role'                              => ['manage', 'create', 'edit', 'delete'],
            'coupon'                            => ['manage', 'create', 'edit', 'delete', 'show'],
            'plan'                              => ['manage', 'create', 'delete', 'show', 'edit'],
            'blog'                              => ['manage', 'create', 'edit', 'delete'],
            'category'                          => ['manage', 'create', 'edit', 'delete'],
            'offline-payment-transactions'      => ['manage'],
            'transactions'                      => ['manage'],
            'email-template'                    => ['manage', 'edit'],
            'sms-template'                      => ['manage', 'edit'],
            'language'                          => ['manage', 'create', 'delete'],
            'setting'                           => ['manage'],
            'landingpage'                       => ['manage'],
            'testimonial'                       => ['manage', 'create', 'edit', 'delete'],
            'faqs'                              => ['manage', 'create', 'edit', 'delete'],
            'page-setting'                      => ['manage', 'create', 'edit', 'delete'],
            'dashboard-widget'                  => ['manage', 'create', 'edit', 'delete'],
            'form-template'                     => ['manage', 'create', 'edit', 'delete', 'design'],
            'form-category'                     => ['manage', 'create', 'edit', 'delete'],
            'form'                              => ['manage', 'create', 'edit', 'delete', 'design', 'fill', 'duplicate', 'theme-setting', 'integration', 'payment' ,'access-all'],
            'form-rule'                         => ['manage', 'create', 'edit', 'delete'],
            'submitted-form'                    => ['show', 'manage', 'edit', 'delete', 'download','access-all'],
            'booking'                           => ['manage', 'create', 'edit', 'delete', 'design', 'payment', 'fill'],
            'booking-calendar'                  => ['manage'],
            'submitted-booking'                 => ['show', 'manage', 'delete', 'copyurl'],
            'poll'                              => ['manage', 'create', 'edit', 'delete', 'vote', 'result'],
            'document'                          => ['manage', 'create', 'edit', 'delete'],
            'chat'                              => ['manage'],
            'transaction'                       => ['manage'],
            'event'                             => ['manage', 'create', 'edit', 'delete'],
            'announcement'                      => ['manage', 'create', 'edit', 'delete'],
        ];

        $adminpermissions = [
            'dashboard-widget'                  => ['manage', 'create', 'edit', 'delete'],
            'user'                              => ['manage', 'create', 'edit', 'delete', 'impersonate', 'plan-upgrade', 'phone-verified', 'email-verified'],
            'role'                              => ['manage', 'create', 'edit', 'delete'],
            'plan'                              => ['manage'],
            'form-template'                     => ['manage', 'create', 'edit', 'delete', 'design'],
            'form-category'                     => ['manage', 'create', 'edit', 'delete'],
            'form'                              => ['manage', 'create', 'edit', 'delete', 'design', 'fill', 'duplicate', 'theme-setting', 'integration', 'payment'],
            'form-rule'                         => ['manage', 'create', 'edit', 'delete'],
            'submitted-form'                    => ['show', 'manage', 'edit', 'delete', 'download'],
            'booking'                           => ['manage', 'create', 'edit', 'delete', 'design', 'payment', 'fill'],
            'booking-calendar'                  => ['manage'],
            'submitted-booking'                 => ['show', 'manage', 'delete', 'copyurl'],
            'poll'                              => ['manage', 'create', 'edit', 'delete', 'vote', 'result'],
            'document'                          => ['manage', 'create', 'edit', 'delete', 'document-generate'],
            'chat'                              => ['manage'],
            'event'                             => ['manage', 'create', 'edit', 'delete'],
            'setting'                           => ['manage'],
            'announcement'                      => ['manage', 'create', 'edit', 'delete'],
        ];

        $settings = [
            ['key' => 'app_name', 'value' => 'Prime Laravel Form Builder Saas', 'created_by' => '1'],
            ['key' => 'app_logo', 'value' => 'app-logo/app-logo.png', 'created_by' => '1'],
            ['key' => 'app_small_logo', 'value' => 'app-logo/app-small-logo.png', 'created_by' => '1'],
            ['key' => 'app_dark_logo', 'value' => 'app-logo/app-dark-logo.png', 'created_by' => '1'],
            ['key' => 'favicon_logo', 'value' => 'app-logo/app-favicon-logo.png', 'created_by' => '1'],
            ['key' => 'default_language', 'value' => 'en', 'created_by' => '1'],
            ['key' => 'approve_type', 'value' => 'Manually', 'created_by' => '1'],
            ['key' => 'transparent_layout', 'value' => 'on', 'created_by' => '1'],
            ['key' => 'storage_type', 'value' => 'local', 'created_by' => '1'],
            ['key' => 'color', 'value' => 'theme-2', 'created_by' => '1'],
            ['key' => 'landing_page_status', 'value' => '1', 'created_by' => '1'],
            ['key' => 'date_format', 'value' => 'M j, Y', 'created_by' => '1'],
            ['key' => 'time_format', 'value' => 'g:i A', 'created_by' => '1'],
            ['key' => 'roles', 'value' => 'User', 'created_by' => '1'],
            ['key' => 'dark_mode', 'value' => 'off', 'created_by' => '1'],
            ['key' => 'pwa_icon_128', 'value' => 'pwa-image/pwa-icon-128.png', 'created_by' => '1'],
            ['key' => 'pwa_icon_144', 'value' => 'pwa-image/pwa-icon-144.png', 'created_by' => '1'],
            ['key' => 'pwa_icon_152', 'value' => 'pwa-image/pwa-icon-152.png', 'created_by' => '1'],
            ['key' => 'pwa_icon_192', 'value' => 'pwa-image/pwa-icon-192.png', 'created_by' => '1'],
            ['key' => 'pwa_icon_256', 'value' => 'pwa-image/pwa-icon-256.png', 'created_by' => '1'],
            ['key' => 'pwa_icon_512', 'value' => 'pwa-image/pwa-icon-512.png', 'created_by' => '1'],
        ];

        foreach ($settings as $setting) {
            settings::firstOrCreate($setting);
        }

        $role = Role::firstOrCreate([
            'name'          => 'Super Admin',
            'created_by'    => '1',
        ]);

        $adminRole = Role::firstOrCreate([
            'name'          => 'Admin',
            'created_by'    => '1',
        ]);

        Plan::firstOrCreate(['name' => 'Free'], [
            'name'              => 'Free',
            'price'             => '0',
            'duration'          => '1',
            'durationtype'      => 'Month',
            'max_users'         => '10',
            'max_roles'         => '10',
            'max_form'          => '10',
            'max_booking'       => '10',
            'max_documents'     => '10',
            'max_polls'         => '10',
            'description'       => 'Despite being a free plan, we provide you with access to the complete feature'
        ]);

        foreach ($permissions as $module => $adminpermission) {
            Module::firstOrCreate(['name' => $module]);
            foreach ($adminpermission as $permission) {
                $temp = Permission::firstOrCreate(['name' => $permission . '-' . $module]);
                $role->givePermissionTo($temp);
            }
        }

        foreach ($adminpermissions as $moduleDB => $adminpermissiondb) {
            Module::firstOrCreate(['name' => $moduleDB]);
            foreach ($adminpermissiondb as $adminpermission) {
                $adminTemp = Permission::firstOrCreate(['name' => $adminpermission . '-' . $moduleDB]);
                $adminRole->givePermissionTo($adminTemp);
            }
        }

        $user = User::firstOrCreate(['name' => 'Super Admin'], [
            'name'              => 'Super Admin',
            'email'             => 'superadmin@example.com',
            'password'          => Hash::make('admin@1232'),
            'active_status'     => '1',
            'avatar'            => ('avatar/avatar.png'),
            'type'              => 'Super Admin',
            'created_by'        => '1',
            'lang'              => 'en',
            'email_verified_at' => Carbon::now()->toDateTimeString(),
            'phone_verified_at' => Carbon::now()->toDateTimeString(),
        ]);

        $user->assignRole($role->id);

        $arrEnv = [
            'CURRENCY'          => 'USD',
            'CURRENCY_SYMBOL'   => '$'
        ];

        UtilityFacades::setEnvironmentValue($arrEnv);

        MailTemplate::firstOrCreate(['subject' => 'Mail send for testing purpose.'], [
            'mailable' => \App\Mail\TestMail::class,
            'subject' => 'Mail send for testing purpose.',
            'html_template' => '<p><strong>This Mail For Testing</strong></p>

            <p><strong>Thanks</strong></p>',
            'text_template' => null,
        ]);

        MailTemplate::firstOrCreate(['subject' => 'Approve Request.'], [
            'mailable' => \App\Mail\ApproveMail::class,
            'subject' => 'Approve Request.',
            'html_template' => '<p><strong>Hi {{name}}</strong></p>

            <p><strong>Yout Request&nbsp;{{ email }}&nbsp;&nbsp;is Verified By SuperAdmin</strong></p>

            <p>&nbsp;</p>',
            'text_template' => null,
        ]);

        MailTemplate::firstOrCreate(['subject' => 'Disapprove Request.'], [
            'mailable' => \App\Mail\DisapprovedMail::class,
            'subject' => 'Disapprove Request.',
            'html_template' => '<p><strong>Hi&nbsp;{{ name }}</strong></p>

            <p><strong>Your Request&nbsp;{{ email }}&nbsp;is not Verified By SuperAdmin </strong></p>

            <p><strong>Because&nbsp;{{ reason }}</strong></p>

            <p><strong>Please contact to SuperAdmin</strong></p>

            <p>&nbsp;</p>',
            'text_template' => null,
        ]);

        MailTemplate::firstOrCreate(['subject' => 'Plan Request Verified.'], [
            'mailable' => \App\Mail\ApproveOfflineMail::class,
            'subject' => 'Plan Request Verified.',
            'html_template' => '<p><strong>Hi&nbsp;&nbsp;{{ name }}</strong></p>

            <p><strong>Your plan is updated.<br />
            plan_name:{{ planName }}<br />
            amount:{{ amount }}<br />
            expire_date:{{ expireDate }}</strong></p>

            <p>&nbsp;</p>

            <p>&nbsp;</p>',
            'text_template' => null,
        ]);

        MailTemplate::firstOrCreate(['subject' => 'Plan Request Unverified.'], [
            'mailable' => \App\Mail\OfflineMail::class,
            'subject' => 'Plan Request Unverified.',
            'html_template' => '<p><strong>Hi&nbsp;{{ name }}</strong></p>

            <p><strong>Your Request Payment {{ email }}&nbsp;Is Disapprove By Super Admin</strong></p>

            <p><strong>Because&nbsp;{{ disapproveReason }}</strong></p>

            <p><strong>Please Contact to Super Admin</strong></p>',
            'text_template' => null,
        ]);

        MailTemplate::firstOrCreate(['subject' => 'New Enquiry Details.'], [
            'mailable' => \App\Mail\ConatctMail::class,
            'subject' => 'New Enquiry Details.',
            'html_template' => '<p><strong>Name : {{name}}</strong></p>

            <p><strong>Email : </strong><strong>{{email}}</strong></p>

            <p><strong>Contact No : {{ contactNo }}&nbsp;</strong></p>

            <p><strong>Message :&nbsp;</strong><strong>{{ message }}</strong></p>',
            'text_template' => null,
        ]);

        MailTemplate::firstOrCreate(['subject' => 'Register Mail.'], [
            'mailable' => \App\Mail\RegisterMail::class,
            'subject' => 'Register Mail.',
            'html_template' => '<p><strong>Hi {{name}}</strong></p>

            <p><strong>Email : {{email}}</strong></p>

            <p><strong>Thanks for registration, your account is in review and you get email when your account active.</strong></p>',
            'text_template' => null,
        ]);

        MailTemplate::firstOrCreate(['mailable' => 'App\Mail\Thanksmail'], [
            'mailable' => 'App\Mail\Thanksmail',
            'subject' => 'New survey Submited - {{ title }}',
            'html_template' => '<div class="section-body">
            <div class="mx-0 row">
            <div class="mx-auto col-6">
            <div class="card">
            <div class="card-header">
            <h4 class="text-center w-100">{{ title }}</h4>
            </div>
            <div class="card-body">
            <div class="text-center">
            <img src="{{image}}" id="app-dark-logo" class="my-5 text-center img img-responsive w-30 justify-content-center"/>
            </div>
            <h2 class="text-center w-100">{{ thanksMsg }}</h2>
            </div>
            </div>
            </div>
            </div>
            </div>',
            'text_template' => null,
        ]);

        MailTemplate::firstOrCreate(['mailable' => 'App\Mail\BookingThanksMail'], [
            'mailable' => 'App\Mail\BookingThanksMail',
            'subject' => 'New booking Submited - {{ title }}',
            'html_template' => '<div class="section-body">
            <div class="mx-0 row">
            <div class="mx-auto col-6">
            <div class="card">
            <div class="card-header">
            <h4 class="text-center w-100">{{ title }}</h4>
            </div>
            <div class="card-body">
            <div class="text-center">
            <img src="{{image}}" id="app-dark-logo" class="my-5 text-center img img-responsive w-30 justify-content-center"/>
            </div>
            <h2 class="text-center w-100">{{ thanksMsg }}</h2>
            <h3 class="text-center w-100">Click to view your booking details: <a target="_blank" href="{{ link }}">Click Here</a></h3>
            </div>
            </div>
            </div>
            </div>
            </div>',
            'text_template' => null,
        ]);

        SmsTemplate::firstOrCreate(['event' => 'verification code sms'], [
            'event'         => 'verification code sms',
            'template'      => 'Hello :name, Your verification code is :code',
            'variables'     => 'name,code'
        ]);

        NotificationsSetting::firstOrCreate(['title' => 'testing purpose'], [
            'title'                 => 'testing purpose',
            'email_notification'    => '1',
            'sms_notification'      => '0',
            'notify'                => '1',
            'status'                => '2',
        ]);

        NotificationsSetting::firstOrCreate(['title' => 'Register mail'], [
            'title'                 => 'Register mail',
            'email_notification'    => '1',
            'sms_notification'      => '2',
            'notify'                => '1',
            'status'                => '1',
        ]);

        NotificationsSetting::firstOrCreate(['title' => 'new survey details'], [
            'title'                 => 'new survey details',
            'email_notification'    => '1',
            'sms_notification'      => '2',
            'notify'                => '1',
            'status'                => '2',
        ]);

        NotificationsSetting::firstOrCreate(['title' => 'new booking survey details'], [
            'title'                 => 'new booking survey details',
            'email_notification'    => '1',
            'sms_notification'      => '2',
            'notify'                => '1',
            'status'                => '2',
        ]);

        NotificationsSetting::firstOrCreate(['title' => 'new enquiry details'], [
            'title'                 => 'new enquiry details',
            'email_notification'    => '1',
            'sms_notification'      => '2',
            'notify'                => '1',
            'status'                => '2',
        ]);

        NotificationsSetting::firstOrCreate(['title' => 'Form Create'], [
            'title'                 => 'Form Create',
            'email_notification'    => '2',
            'sms_notification'      => '2',
            'notify'                => '0',
            'status'                => '1',
        ]);


    }
}
