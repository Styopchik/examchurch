<?php

namespace App\Http\Middleware;

use App\Facades\UtilityFacades;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Setting
{
    public function handle($request, Closure $next)
    {
        if (!file_exists(storage_path() . "/installed")) {
            header('location:install');
            die;
        }

        config([
            'chatify.routes.middleware'                                         => env('CHATIFY_ROUTES_MIDDLEWARE', ['web', 'auth', 'Setting'])
        ]);

        config([
            'app.name'                                                          => UtilityFacades::getsettings('app_name'),

            'mail.default'                                                      => UtilityFacades::getsettings('mail_mailer'),
            'mail.mailers.smtp.host'                                            => UtilityFacades::getsettings('mail_host'),
            'mail.mailers.smtp.port'                                            => UtilityFacades::getsettings('mail_port'),
            'mail.mailers.smtp.encryption'                                      => UtilityFacades::getsettings('mail_encryption'),
            'mail.mailers.smtp.username'                                        => UtilityFacades::getsettings('mail_username'),
            'mail.mailers.smtp.password'                                        => UtilityFacades::getsettings('mail_password'),
            'mail.from.address'                                                 => UtilityFacades::getsettings('mail_from_address'),
            'mail.from.name'                                                    => UtilityFacades::getsettings('mail_from_name'),

            'chatify.pusher.key'                                                => UtilityFacades::getsettings('pusher_key'),
            'chatify.pusher.secret'                                             => UtilityFacades::getsettings('pusher_secret'),
            'chatify.pusher.app_id'                                             => UtilityFacades::getsettings('pusher_id'),
            'chatify.pusher.options.cluster'                                    => UtilityFacades::getsettings('pusher_cluster'),

            'captcha.sitekey'                                                   => UtilityFacades::getsettings('recaptcha_key'),
            'captcha.secret'                                                    => UtilityFacades::getsettings('recaptcha_secret'),

            'services.google.client_id'                                         => UtilityFacades::getsettings('google_client_id', ''),
            'services.google.client_secret'                                     => UtilityFacades::getsettings('google_client_secret', ''),
            'services.google.redirect'                                          => UtilityFacades::getsettings('google_redirect', ''),

            'services.facebook.client_id'                                       => UtilityFacades::getsettings('facebook_client_id', ''),
            'services.facebook.client_secret'                                   => UtilityFacades::getsettings('facebook_client_secret', ''),
            'services.facebook.redirect'                                        => UtilityFacades::getsettings('facebook_redirect', ''),

            'services.github.client_id'                                         => UtilityFacades::getsettings('github_client_id', ''),
            'services.github.client_secret'                                     => UtilityFacades::getsettings('github_client_secret', ''),
            'services.github.redirect'                                          => UtilityFacades::getsettings('github_redirect', ''),

            'services.linkedin.client_id'                                       => UtilityFacades::getsettings('linkedin_client_id', ''),
            'services.linkedin.client_secret'                                   => UtilityFacades::getsettings('linkedin_client_secret', ''),
            'services.linkedin.redirect'                                        => UtilityFacades::getsettings('linkedin_redirect', ''),

            'services.paytm.env'                                                => UtilityFacades::keysettings('paytm_environment', 1),
            'services.paytm.merchant_id'                                        => UtilityFacades::keysettings('paytm_merchant_id', 1),
            'services.paytm.merchant_key'                                       => UtilityFacades::keysettings('paytm_merchant_key', 1),
            'services.paytm.merchant_website'                                   => UtilityFacades::keysettings('paytm_merchant_website', 1),
            'services.paytm.channel'                                            => UtilityFacades::keysettings('paytm_channel', 1),
            'services.paytm.industry_type'                                      => UtilityFacades::keysettings('paytm_industry_type', 1),

            'paypal.mode'                                                       => UtilityFacades::keysettings('paypal_mode',1),
            'paypal.sandbox.client_id'                                          => UtilityFacades::keysettings('paypal_sandbox_client_id', 1),
            'paypal.sandbox.client_secret'                                      => UtilityFacades::keysettings('paypal_sandbox_client_secret', 1),
            'paypal.sandbox.app_id'                                             => 'APP-80W284485P519543T',

            'google-calendar.default_auth_profile'                              => 'service_account',
            'google-calendar.auth_profiles.service_account.credentials_json'    => Storage::path('google-json-file/'.UtilityFacades::getsettings('google_calendar_json_file')),
            'google-calendar.auth_profiles.oauth.credentials_json'              => Storage::path('google-json-file/'.UtilityFacades::getsettings('google_calendar_json_file')),
            'google-calendar.auth_profiles.oauth.token_json'                    => Storage::path('google-json-file/'.UtilityFacades::getsettings('google_calender_json_file')),
            'google-calendar.calendar_id'                                       => UtilityFacades::getsettings('google_calendar_id'),

            'seotools.meta.defaults.description'                                => UtilityFacades::getsettings('meta_description'),
            'seotools.meta.defaults.keywords'                                   => explode(',', UtilityFacades::getsettings('meta_keywords')),

            'seotools.opengraph.defaults.title'                                 => UtilityFacades::getsettings('meta_title'),
            'seotools.opengraph.defaults.description'                           => UtilityFacades::getsettings('meta_description'),
            'seotools.opengraph.defaults.image'                                 => UtilityFacades::getpath(UtilityFacades::getsettings('meta_image')).'?'.time(),
            'seotools.opengraph.defaults.locale'                                => 'en_US',
            'seotools.opengraph.defaults.type'                                  => 'website',
            'seotools.opengraph.defaults.site_name'                             => config('app.name'),

            'seotools.twitter.defaults.card'                                    => 'summary_large_image',
            'seotools.twitter.defaults.title'                                   => UtilityFacades::getsettings('meta_title'),
            'seotools.twitter.defaults.description'                             => UtilityFacades::getsettings('meta_description'),
            'seotools.twitter.defaults.image'                                   => UtilityFacades::getpath(UtilityFacades::getsettings('meta_image')).'?'.time(),
            'seotools.twitter.defaults.site'                                    => '@Prime',

            'seotools.json-ld.defaults.title'                                   => UtilityFacades::getsettings('meta_title'),
            'seotools.json-ld.defaults.description'                             => UtilityFacades::getsettings('meta_description'),
            'seotools.json-ld.defaults.image'                                   => UtilityFacades::getpath(UtilityFacades::getsettings('meta_image')).'?'.time(),
        ]);
        return $next($request);
    }
}
