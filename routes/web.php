<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\Payment\BKashPaymentController;
use App\Http\Controllers\BlogCategoryController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingValueController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\CommentsReplyController;
use App\Http\Controllers\Payment\CashFreeController;
use App\Http\Controllers\Payment\CoingateController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Payment\PayPalController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\Payment\RazorpayPaymentController;
use App\Http\Controllers\RequestuserController;
use App\Http\Controllers\SocialLoginController;
use App\Http\Controllers\OfflineRequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\Payment\FlutterwaveController;
use App\Http\Controllers\FormCommentsController;
use App\Http\Controllers\FormCommentsReplyController;
use App\Http\Controllers\Payment\PaystackController;
use App\Http\Controllers\Payment\PaytmController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\DashboardWidgetController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentGenratorController;
use App\Http\Controllers\DocumentMenuController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FormTemplateController;
use App\Http\Controllers\FormValueController;
use App\Http\Controllers\FromCategoryController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LoginSecurityController;
use App\Http\Controllers\Payment\MercadoController;
use App\Http\Controllers\NotificationsSettingController;
use App\Http\Controllers\PageSettingController;
use App\Http\Controllers\Payment\PayTabController;
use App\Http\Controllers\Payment\PayUMoneyController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Payment\StripeController;
use App\Http\Controllers\SmsTemplateController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\Payment\SSPayController;
use App\Http\Controllers\SystemAnalyticsController;
use App\Http\Controllers\TestimonialController;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

require_once __DIR__ . '/auth.php';
// 'verified_phone'  middleware
Route::group(['middleware' => ['auth', 'Setting', 'Upload', 'xss', 'verified', 'verified_phone', '2fa']], function () {
    Route::resource('users', UserController::class)->except(['show']);
    Route::get('account-status/{id}', [UserController::class, 'accountStatus'])->name('account.status');
    Route::post('user/status/{id}', [UserController::class, 'userStatus'])->name('users.status');
    Route::get('user/{id}/plan', [UserController::class, 'userPlan'])->name('user.plan');
    Route::get('user/{user_id}/plan/{plan_id}', [UserController::class, 'userPlanAssign'])->name('user.plan.assign');
    Route::get('users/verified/{id}', [UserController::class, 'useremailverified'])->name('user.verified');
    Route::get('users/phoneverified/{id}', [UserController::class, 'userphoneverified'])->name('user.phoneverified');
    Route::get('users/grid/{id?}', [UserController::class, 'gridView'])->name('grid.view');
    Route::resource('roles', RoleController::class);
    Route::resource('module', ModuleController::class);
    Route::resource('formvalues', FormValueController::class);
    Route::resource('plans', PlanController::class);
    Route::post('plan-status/{id}', [PlanController::class, 'planStatus'])->name('plan.status');
    Route::get('offline', [OfflineRequestController::class, 'index'])->name('offline.index');
    Route::delete('offline/destroy/{id}', [OfflineRequestController::class, 'destroy'])->name('offline.destroy');
    Route::resource('poll', PollController::class);
    Route::resource('coupon', CouponController::class);
    Route::resource('faqs', FaqController::class);
    Route::resource('forms', FormController::class)->except(['show']);
    Route::resource('blogs', BlogController::class)->except(['show']);
    Route::resource('blogs-category', BlogCategoryController::class);
    Route::post('blog-category-status/{id}', [BlogCategoryController::class, 'blogCategoryStatus'])->name('blogcategory.status');
    Route::get('forms/add', [FormController::class, 'addForm'])->name('forms.add');
    Route::get('forms/use/template/{id}', [FormController::class, 'useFormtemplate'])->name('forms.use.template');

     // form category
     Route::resource('form-category', FromCategoryController::class)->except(['show']);
     Route::post('formcategory-status/{id}', [FromCategoryController::class,'formCategoryStatus'])->name('formcategory.status');

    Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('profile/update', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('home', [HomeController::class, 'index'])->name('home');
    Route::post('read/notification', [HomeController::class, 'readNotification'])->name('read.notification');
    Route::post('change/theme/mode', [HomeController::class, 'changeThememode'])->name('change.theme.mode');

    // dashboard chart
    Route::post('chart', [HomeController::class, 'formChart'])->name('get.chart.data');

    //Coupon
    Route::get('coupon/csv/upload', [CouponController::class, 'uploadCsv'])->name('coupon.upload');
    Route::post('coupon/csv/upload/store', [CouponController::class, 'uploadCsvStore'])->name('coupon.upload.store');
    Route::get('coupon/mass/create', [CouponController::class, 'massCreate'])->name('coupon.mass.create');
    Route::post('coupon/mass/store', [CouponController::class, 'massCreateStore'])->name('coupon.mass.store');
    Route::get('coupon/status/{id}', [CouponController::class, 'couponStatus'])->name('coupon.status');

    //Form Template
    Route::resource('form-template', FormTemplateController::class);
    Route::post('form-template/status/{id}', [FormTemplateController::class, 'status'])->name('formTemplate.status');
    Route::get('form-template/design/{id}', [FormTemplateController::class, 'design'])->name('formTemplate.design');
    Route::put('form-template/design/update/{id}', [FormTemplateController::class, 'designUpdate'])->name('formTemplate.design.update');


    //Booking
    Route::resource('bookings', BookingController::class);
    Route::get('bookings/design/{id}', [BookingController::class, 'design'])->name('booking.design');
    Route::put('bookings/design/update/{id}', [BookingController::class, 'designUpdate'])->name('booking.design.update');
    Route::get('bookings/slots/setting/{id}', [BookingController::class, 'slotsSetting'])->name('booking.slots.setting');
    Route::post('bookings/slots/setting/update/{id}', [BookingController::class, 'slotsSettingUpdate'])->name('booking.slots.setting.update')->withoutMiddleware('xss');
    Route::get('bookings/slots/time/appoinment/{id}', [BookingController::class, 'appoinmentTime'])->name('booking.appoinment.time');
    Route::get('bookings/slots/seat/appoinment/{id}', [BookingController::class, 'appoinmentSeat'])->name('booking.appoinment.seat');
    Route::get('bookings/payment/integration/{id}', [BookingController::class, 'bookingPaymentIntegration'])->name('booking.payment.integration');
    Route::post('bookings/payment/integration/store/{id}', [BookingController::class, 'bookingPaymentIntegrationstore'])->name('booking.payment.integration.store');
    Route::get('calendar/bookings', [BookingController::class, 'bookingCalendar'])->name('booking.calendar');
    Route::post('bookings/dropzone/upload/{id}', [BookingController::class, 'dropzone'])->name('booking.dropzone.upload')->middleware(['Setting', 'Upload']);

    //booking value
    Route::get('booking-values/{id}/view', [BookingValueController::class, 'showBookingsForms'])->name('view.booking.values');
    Route::delete('booking-values/destroy/{id}', [BookingValueController::class, 'destroy'])->name('bookingvalues.destroy');
    Route::get('booking-values/time/{id}/view', [BookingValueController::class, 'timingBookingValuesShow'])->name('timing.bookingvalues.show');
    Route::get('booking-values/seats/{id}/view', [BookingValueController::class, 'seatsBookingValuesShow'])->name('seats.bookingvalues.show');

    // Role
    Route::post('role-permission/{id}', [RoleController::class, 'assignPermission'])->name('roles.permit');

    // Profile Photo Update
    Route::get('update-avatar', [ProfileController::class, 'showAvatar'])->name('updateAvatar');
    Route::post('update-avatar', [ProfileController::class, 'updateAvatar']);
    Route::post('update-profile-login', [ProfileController::class, 'updateLogin'])->name('update.login');
    Route::get('profile-status', [ProfileController::class, 'profileStatus'])->name('profile.status');

    // PDF download
    Route::get('form-values/{id}/download/pdf', [FormValueController::class, 'downloadPdf'])->name('download.form.values.pdf')->middleware(['auth', 'Setting', 'xss']);

    // Form Intigration
    Route::get('forms/slack/integration/{id}', [FormController::class, 'slackIntegration'])->name('slack.integration');
    Route::get('forms/telegram/integration/{id}', [FormController::class, 'telegramIntegration'])->name('telegram.integration');
    Route::get('forms/mailgun/integration/{id}', [FormController::class, 'mailgunIntegration'])->name('mailgun.integration');
    Route::get('forms/bulkgate/integration/{id}', [FormController::class, 'bulkgateIntegration'])->name('bulkgate.integration');
    Route::get('forms/nexmo/integration/{id}', [FormController::class, 'nexmoIntegration'])->name('nexmo.integration');
    Route::get('forms/fast2sms/integration/{id}', [FormController::class, 'fast2smsIntegration'])->name('fast2sms.integration');
    Route::get('forms/vonage/integration/{id}', [FormController::class, 'vonageIntegration'])->name('vonage.integration');
    Route::get('forms/sendgrid/integration/{id}', [FormController::class, 'sendgridIntegration'])->name('sendgrid.integration');
    Route::get('forms/twilio/integration/{id}', [FormController::class, 'twilioIntegration'])->name('twilio.integration');
    Route::get('forms/textlocal/integration/{id}', [FormController::class, 'textlocalIntegration'])->name('textlocal.integration');
    Route::get('forms/messente/integration/{id}', [FormController::class, 'messenteIntegration'])->name('messente.integration');
    Route::get('forms/smsgateway/integration/{id}', [FormController::class, 'smsgatewayIntegration'])->name('smsgateway.integration');
    Route::get('forms/clicktell/integration/{id}', [FormController::class, 'clicktellIntegration'])->name('clicktell.integration');
    Route::get('forms/clockwork/integration/{id}', [FormController::class, 'clockworkIntegration'])->name('clockwork.integration');

    Route::get('forms/integration/{id}', [FormController::class, 'formIntegration'])->name('form.integration');
    Route::get('forms/payment/integration/{id}', [FormController::class, 'formPaymentIntegration'])->name('form.payment.integration');
    Route::post('forms/payment/integration/store/{id}', [FormController::class, 'formPaymentIntegrationStore'])->name('form.payment.integration.store');
    Route::post('forms/integration/{id}', [FormController::class, 'formIntegrationStore'])->name('form.integration.store');
    Route::get('forms/themes/{id}', [FormController::class, 'formTheme'])->name('form.theme');
    Route::get('forms/themes/edit/{theme}/{id}', [FormController::class, 'formThemeEdit'])->name('form.theme.edit');
    Route::post('forms/themes/update/{id}', [FormController::class, 'formThemeUpdate'])->name('form.theme.update');
    Route::post('forms/themes/change/{id}', [FormController::class, 'themeChange'])->name('form.theme.change');

    //document
    Route::resource('document', DocumentGenratorController::class)->except(['show']);
    Route::get('document/design/{id}', [DocumentGenratorController::class, 'design'])->name('document.design');

    //status drag-drop
    Route::post('document/designmenu', [DocumentGenratorController::class, 'updateDesign'])->name('updatedesign.document');
    Route::get('document-status/{id}', [DocumentGenratorController::class, 'documentStatus'])->name('document.status');

    // menu
    Route::get('docmenu/index', [DocumentMenuController::class, 'index'])->name('docmenu.index');
    Route::get('docmenu/create/{docmenu_id}', [DocumentMenuController::class, 'create'])->name('docmenu.create');
    Route::post('docmenu/store', [DocumentMenuController::class, 'store'])->name('docmenu.store');
    Route::delete('document/menu/{id}', [DocumentMenuController::class, 'destroy'])->name('document.design.delete');

    // submenu
    Route::get('docsubmenu/create/{id}/{docmenu_id}', [DocumentMenuController::class, 'submenuCreate'])->name('docsubmenu.create');
    Route::post('docsubmenu/store', [DocumentMenuController::class, 'submenuStore'])->name('docsubmenu.store');
    Route::delete('document/submenu/{id}', [DocumentMenuController::class, 'submenuDestroy'])->name('document.submenu.design.delete');

    //sms
    Route::resource('sms-template', SmsTemplateController::class);

    // Email templates
    Route::resource('email-template', EmailTemplateController::class);

    //Event Calender
    Route::resource('event', EventController::class);
    Route::post('event/showlist', [EventController::class, 'showEventList'])->name('show.event.list');

    //dashboard-create
    Route::resource('dashboard', DashboardController::class)->except(['show']);;
    Route::post('update/dashboard', [DashboardController::class, 'updateDashboard'])->name('update.dashboard');
    Route::post('widget/chnages', [DashboardController::class, 'widgetChnages'])->name('widget.chnages');
    Route::post('widget/chartdata', [DashboardWidgetController::class, 'widgetChartData'])->name('widget.chartdata');

    // Testimonials
    Route::resource('testimonials', TestimonialController::class);
    Route::post('testimonials/status/{id}', [TestimonialController::class, 'status'])->name('testimonials.status')->middleware(['xss']);

    // Settings
    Route::post('notification/status/{id}', [NotificationsSettingController::class, 'changeStatus'])->name('notification.status.change');
    Route::post('ckeditor/upload', [SettingsController::class, 'upload'])->name('ckeditor.upload');
    Route::get('settings', [SettingsController::class, 'index'])->name('settings');
    Route::get('test-mail', [SettingsController::class, 'testMail'])->name('test.mail');
    Route::post('test-mail', [SettingsController::class, 'testSendMail'])->name('test.send.mail');
    Route::post('settings/app-name/update', [SettingsController::class, 'appNameUpdate'])->name('settings.appName.update');
    Route::post('settings/pusher-setting/update', [SettingsController::class, 'pusherSettingUpdate'])->name('settings.pusherSetting.update');
    Route::post('settings/wasabi-setting/update', [SettingsController::class, 'wasabiSettingUpdate'])->name('settings.wasabiSetting.update');
    Route::post('settings/captcha-setting/update', [SettingsController::class, 'captchaSettingUpdate'])->name('settings.captchaSetting.update');
    Route::post('settings/payment-setting/update', [SettingsController::class, 'paymentSettingUpdate'])->name('settings.paymentSetting.update');
    Route::post('settings/social-setting/update', [SettingsController::class, 'socialSettingUpdate'])->name('settings.socialSetting.update');
    Route::post('settings/email-setting/update', [SettingsController::class, 'emailSettingUpdate'])->name('settings.emailSetting.update');
    Route::post('settings/google-calender/update', [SettingsController::class, 'googleSettingUpdate'])->name('settings.googleCalender.update');
    Route::post('settings/google-map/update', [SettingsController::class, 'googleMapUpdate'])->name('settings.googleMap.update');
    Route::post('settings/general-setting/update', [SettingsController::class, 'authSettingsUpdate'])->name('settings.generalSetting.update');
    Route::post('settings/seo-setting/update', [SettingsController::class, 'seoSettingsUpdate'])->name('settings.seoSetting.update');
    Route::post('settings/cookie-setting/update', [SettingsController::class, 'cookieSettingUpdate'])->name('settings.cookieSetting.update');
    Route::post('settings/sms-setting/update', [SettingsController::class, 'smsSettingUpdate'])->name('settings.smsSetting.update');
    Route::post('settings/pwa-setting/update', [SettingsController::class, 'pwaSettingUpdate'])->name('settings.pwa-setting.update');

    // Page Settings
    Route::resource('page-setting', PageSettingController::class);

    //froentend
    Route::group(['prefix' => 'landingpage-setting'], function () {
        Route::get('app-setting', [LandingPageController::class, 'landingPageSetting'])->name('landingpage.setting');
        Route::post('app-setting/store', [LandingPageController::class, 'appSettingStore'])->name('landing.app.store');

        Route::get('menu-setting', [LandingPageController::class, 'menuSetting'])->name('menusetting.index');
        Route::post('menu-setting-section1/store', [LandingPageController::class, 'menuSettingSection1Store'])->name('landing.menusection1.store');
        Route::post('menu-setting-section2/store', [LandingPageController::class, 'menuSettingSection2Store'])->name('landing.menusection2.store');
        Route::post('menu-setting-section3/store', [LandingPageController::class, 'menuSettingSection3Store'])->name('landing.menusection3.store');

        Route::get('feature-setting', [LandingPageController::class, 'featureSetting'])->name('landing.feature.index');
        Route::post('feature-setting/store', [LandingPageController::class, 'featureSettingStore'])->name('landing.feature.store');
        Route::get('feature/create', [LandingPageController::class, 'featureCreate'])->name('feature.create');
        Route::post('feature/store', [LandingPageController::class, 'featureStore'])->name('feature.store');
        Route::get('feature/edit/{key}', [LandingPageController::class, 'featureEdit'])->name('feature.edit');
        Route::post('feature/update/{key}', [LandingPageController::class, 'featureUpdate'])->name('feature.update');
        Route::delete('feature/delete/{key}', [LandingPageController::class, 'featureDelete'])->name('feature.delete');

        Route::get('business-growth-setting', [LandingPageController::class, 'businessGrowthSetting'])->name('landing.business.growth.index');
        Route::post('business-growth-setting/store', [LandingPageController::class, 'businessGrowthSettingStore'])->name('landing.business.growth.store');
        Route::get('business-growth/create', [LandingPageController::class, 'businessGrowthCreate'])->name('business.growth.create');
        Route::post('business-growth/store', [LandingPageController::class, 'businessGrowthStore'])->name('business.growth.store');
        Route::get('business-growth/edit/{key}', [LandingPageController::class, 'businessGrowthEdit'])->name('business.growth.edit');
        Route::post('business-growth/update/{key}', [LandingPageController::class, 'businessGrowthUpdate'])->name('business.growth.update');
        Route::delete('business-growth/delete/{key}', [LandingPageController::class, 'businessGrowthDelete'])->name('business.growth.delete');

        Route::get('business-growth-view/create', [LandingPageController::class, 'businessGrowthViewCreate'])->name('business.growth.view.create');
        Route::post('business-growth-view/store', [LandingPageController::class, 'businessGrowthViewStore'])->name('business.growth.view.store');
        Route::get('business-growth-view/edit/{key}', [LandingPageController::class, 'businessGrowthViewEdit'])->name('business.growth.view.edit');
        Route::post('business-growth-view/update/{key}', [LandingPageController::class, 'businessGrowthViewUpdate'])->name('business.growth.view.update');
        Route::delete('business-growth-view/delete/{key}', [LandingPageController::class, 'businessGrowthViewDelete'])->name('business.growth.view.delete');

        Route::get('start-view-setting', [LandingPageController::class, 'startViewSetting'])->name('landing.start.view.index');
        Route::post('start-view-setting/store', [LandingPageController::class, 'startViewSettingStore'])->name('landing.start.view.store');

        Route::get('faq-setting', [LandingPageController::class, 'faqSetting'])->name('landing.faq.index');
        Route::post('faq-setting/store', [LandingPageController::class, 'faqSettingStore'])->name('landing.faq.store');

        Route::get('contactus-setting', [LandingPageController::class, 'contactusSetting'])->name('landing.contactus.index');
        Route::post('contactus-setting/store', [LandingPageController::class, 'contactusSettingStore'])->name('landing.contactus.store');

        Route::get('plan-setting', [LandingPageController::class, 'planSetting'])->name('landing.plan.index');
        Route::post('plan-setting/store', [LandingPageController::class, 'planSettingStore'])->name('landing.plan.store');

        Route::get('testimonials-setting', [LandingPageController::class, 'testimonialSetting'])->name('landing.testimonials.index');
        Route::post('testimonials-setting/store', [LandingPageController::class, 'testimonialSettingStore'])->name('landing.testimonials.store');

        Route::get('login-setting', [LandingPageController::class, 'loginSetting'])->name('landing.login.index');
        Route::post('login-setting/store', [LandingPageController::class, 'loginSettingStore'])->name('landing.login.store');

        Route::get('recaptcha-setting', [LandingPageController::class, 'recaptchaSetting'])->name('landing.recaptcha.index');
        Route::post('recaptcha-setting/store', [LandingPageController::class, 'recaptchaSettingStore'])->name('landing.recaptcha.store');

        Route::get('announcements-setting', [LandingPageController::class, 'announcementsSetting'])->name('landing.announcements.index');
        Route::post('announcements-setting/store', [LandingPageController::class, 'announcementsSettingStore'])->name('landing.announcements.store');

        Route::get('blog-setting', [LandingPageController::class, 'blogSetting'])->name('landing.blog.index');
        Route::post('blog-setting/store', [LandingPageController::class, 'blogSettingStore'])->name('landing.blog.store');

        Route::get('footer-setting', [LandingPageController::class, 'footerSetting'])->name('landing.footer.index');
        Route::post('footer-setting/store', [LandingPageController::class, 'footerSettingStore'])->name('landing.footer.store');

        //Footer settings
        //Main Menu
        Route::get('main/menu/create', [LandingPageController::class, 'footerMainMenuCreate'])->name('footer.main.menu.create');
        Route::post('main/menu/store', [LandingPageController::class, 'footerMainMenuStore'])->name('footer.main.menu.store');
        Route::get('main/menu/edit/{id}', [LandingPageController::class, 'footerMainMenuEdit'])->name('footer.main.menu.edit');
        Route::post('main/menu/update/{id}', [LandingPageController::class, 'footerMainMenuUpdate'])->name('footer.main.menu.update');
        Route::delete('main/menu/delete/{id}', [LandingPageController::class, 'footerMainMenuDelete'])->name('footer.main.menu.delete');
        // Sub menu
        Route::get('sub/menu/create', [LandingPageController::class, 'footerSubMenuCreate'])->name('footer.sub.menu.create');
        Route::post('sub/menu/store', [LandingPageController::class, 'footerSubMenuStore'])->name('footer.sub.menu.store');
        Route::get('sub/menu/edit/{id}', [LandingPageController::class, 'footerSubMenuEdit'])->name('footer.sub.menu.edit');
        Route::post('sub/menu/update/{id}', [LandingPageController::class, 'footerSubMenuUpdate'])->name('footer.sub.menu.update');
        Route::delete('sub/menu/delete/{id}', [LandingPageController::class, 'footerSubMenuDelete'])->name('footer.sub.menu.delete');

        //Header settings
        Route::get('header-setting', [LandingPageController::class, 'headerSetting'])->name('landing.header.index');

        // Sub menu
        Route::get('headersub/menu/create', [LandingPageController::class, 'headerSubMenuCreate'])->name('header.sub.menu.create');
        Route::post('headersub/menu/store', [LandingPageController::class, 'headerSubMenuStore'])->name('header.sub.menu.store');
        Route::get('headersub/menu/edit/{id}', [LandingPageController::class, 'headerSubMenuEdit'])->name('header.sub.menu.edit');
        Route::post('headersub/menu/update/{id}', [LandingPageController::class, 'headerSubMenuUpdate'])->name('header.sub.menu.update');
        Route::delete('headersub/menu/delete/{id}', [LandingPageController::class, 'headerSubMenuDelete'])->name('header.sub.menu.delete');

        Route::get('page-background-setting', [LandingPageController::class, 'pageBackground'])->name('landing.page.background.index');
        Route::post('page-background-setting/store', [LandingPageController::class, 'pageBackgroundStore'])->name('landing.page.background.store');
    });

    // system analytics - telescope - pulse dashboard
    Route::get('telescope/{option?}', [SystemAnalyticsController::class, 'telescope'])->name('telescope');

    //plans
    Route::get('payment/{code}', [PlanController::class, 'payment'])->name('payment');

    // STRIPE
    Route::post('stripe/pending', [StripeController::class, 'stripePostPending'])->name('stripe.pending');
    Route::post('stripe', [StripeController::class, 'stripeSession'])->name('stripe.session');
    Route::post('payment-pedning', [StripeController::class, 'paymentPending'])->name('stripe.pending.pay');
    Route::get('payment-success/{id}', [StripeController::class, 'paymentSuccess'])->name('stripe.success.pay');
    Route::get('payment-cancel/{id}', [StripeController::class, 'paymentCancel'])->name('stripe.cancel.pay');

    // RAZORPAY
    Route::post('razorpay/payment', [RazorpayPaymentController::class, 'razorPayPayment'])->name('payrazorpay.payment');
    Route::get('razorpay/transaction/callback/{transaction_id}/{coupoun_id}/{plan_id}', [RazorpayPaymentController::class, 'razorpayCallback']);

    // PAYUMONEY
    Route::post('payumoney/payment', [PayUMoneyController::class, 'payumoneyPayment'])->name('payumoney.payment.init');
    Route::get('payumoney/transaction/callback/{transaction_id}/{coupoun_id}/{plan_id}', [PayUMoneyController::class, 'payUmoneyCallback']);
    Route::any('payumoney/success/{id}', [PayUMoneyController::class, 'payuSuccess'])->name('payu.success');
    Route::any('payumoney/failure/{id}', [PayUMoneyController::class, 'payuFailure'])->name('payu.failure');

    //PAYPAL
    Route::post('process-transactionadmin', [PayPalController::class, 'processTransactionAdmin'])->name('payprocessTransactionadmin');
    Route::get('success-transaction/{data}', [PayPalController::class, 'successTransaction'])->name('paysuccessTransaction');
    Route::get('cancel-transaction/{data}', [PayPalController::class, 'cancelTransaction'])->name('paycancelTransaction');

    // PAYSTACK
    Route::post('paystack/payment', [PaystackController::class, 'paystackPayment'])->name('paypaystack.payment');
    Route::get('paystack/transaction/callback/{transaction_id}/{coupoun_id}/{plan_id}', [PaystackController::class, 'paystackCallback']);

    // CASHFREE
    Route::post('cashfree/payment', [CashFreeController::class, 'cashfreePayment'])->name('cashfree.payment.prepare');
    Route::get('cashfree/transaction/callback', [CashFreeController::class, 'cashfreeCallback'])->name('cashfree.payment.callback');

    // SSPAY
    Route::post('sspay/payment', [SSPayController::class, 'initPayment'])->name('sspay.payment.init');
    Route::get('sspay/transaction/callback', [SSPayController::class, 'sspayCallback'])->name('sspay.payment.callback');

    // PAYTAB
    Route::post('plan/with-paytab', [PayTabController::class, 'planPayWithPaytab'])->name('admin.pay.with.paytab');
    Route::any('plan/paytab-success/plan', [PayTabController::class, 'paytabGetPayment'])->name('admin.paytab.success');

    // FLUTTERWAVE
    Route::post('flutterwave/payment', [FlutterwaveController::class, 'flutterwavePayment'])->name('payflutterwave.payment');
    Route::get('flutterwave/transaction/callback/{transaction_id}/{coupoun_id}/{plan_id}', [FlutterwaveController::class, 'flutterwaveCallback']);

    // COINGATE
    Route::post('coingate/prepare', [CoingateController::class, 'coingatePrepare'])->name('coingate.payment.prepare');
    Route::get('coingate-success/{id}', [CoingateController::class, 'coingateCallback'])->name('coingate.payment.callback');

    // PAYTM
    Route::post('paypayment', [PaytmController::class, 'pay'])->name('paypaytm.payment');
    Route::post('paypayment/callback', [PaytmController::class, 'paymentCallback'])->name('paypaytm.callback');

    // OFFLINE
    Route::post('offline-payment', [OfflineRequestController::class, 'offlinePaymentEntry'])->name('offline.payment.request');

    // MERCADO
    Route::post('mercado/prepare', [MercadoController::class, 'mercadoPrepare'])->name('mercado.payment.prepare');
    Route::any('mercado-payment-callback/{id}', [MercadoController::class, 'mercadoCallback'])->name('mercado.payment.callback');

    //bkash
    Route::post('bkash/token', [BKashPaymentController::class, 'token'])->name('bkash.token');
    Route::post('bkash/pay/init', [BKashPaymentController::class, 'paymentInit'])->name('bkash.pay.init');
    Route::get('bkash/createpayment', [BKashPaymentController::class, 'createPayment'])->name('bkash.createpayment');
    Route::get('bkash/executepayment', [BKashPaymentController::class, 'executePayment'])->name('bkash.executepayment');
});

//sms
Route::group(['middleware' => ['Setting', 'Upload', 'xss']], function () {
    Route::get('sms/notice', [SmsController::class, 'smsNoticeIndex'])->name('smsindex.noticeverification');
    Route::post('sms/notice', [SmsController::class, 'smsNoticeVerify'])->name('sms.noticeverification');
    Route::get('sms/verify', [SmsController::class, 'smsIndex'])->name('smsindex.verification');
    Route::post('sms/verify', [SmsController::class, 'smsVerify'])->name('sms.verification');
    Route::post('sms/verifyresend', [SmsController::class, 'smsResend'])->name('sms.verification.resend');
});

Route::group(['middleware' => ['xss', 'Upload']], function () {
    // UnKnown Users
    Route::get('request-user', [RequestuserController::class, 'index'])->name('requestuser.index');
    Route::get('request-user/{id}/edit', [RequestuserController::class, 'edit'])->name('requestuser.edit');
    Route::post('approve-mail', [RequestuserController::class, 'approveSendMail'])->name('approve.send.mail');
    Route::post('user/update', [RequestuserController::class, 'update'])->name('create.user')->middleware('Setting');
    Route::delete('request-user/{id}/delete', [RequestuserController::class, 'destroy'])->name('requestuser.destroy');
    Route::post('request-user/{id}/update', [RequestuserController::class, 'dataUpdate'])->name('requestuser.update');
    Route::get('request-user/approve/{id}', [RequestuserController::class, 'approveStatus'])->name('approverequestuser.status');
    Route::get('request-user/disapprove/{id}', [RequestuserController::class, 'disapproveStatus'])->name('requestuser.disapprovestatus')->middleware('Setting');
    Route::get('request-user/payment/{id}', [RequestuserController::class, 'payment'])->name('requestuser.payment');
    Route::post('request-user/store', [RequestuserController::class, 'store'])->name('requestuser.store')->middleware(['Setting']);
    Route::get('request/create/{id}', [RequestuserController::class, 'create'])->name('requestuser.create');

    // Offline request
    Route::get('offline-request/{id}', [OfflineRequestController::class, 'offlineRequestStatus'])->name('offlinerequest.status')->middleware('Setting');
    Route::post('request-user/disapprove/{id}', [RequestuserController::class, 'disapprove'])->name('requestuser.disapprove')->middleware('Setting');
    Route::post('offline-request/disapprove-update/{id}', [OfflineRequestController::class, 'offlineDisapprove'])->name('requestuser.disapprove.update')->middleware('Setting');
    Route::get('offline-request/disapprove/{id}', [OfflineRequestController::class, 'disapproveStatus'])->name('offline.disapprove.status')->middleware('Setting');
    Route::get('sales/index', [HomeController::class, 'sales'])->name('sales.index');
});

Route::group(
    ['middleware' => ['xss', 'Upload']],
    function () {
        // Change Language
        Route::get('change-language/{lang}', [LanguageController::class, 'changeLanquage'])->name('change.language')->middleware(['auth']);
        Route::get('manage-language/{lang}', [LanguageController::class, 'manageLanguage'])->name('manage.language')->middleware(['auth']);
        Route::post('store-language-data/{lang}', [LanguageController::class, 'storeLanguageData'])->name('store.language.data')->middleware(['auth']);
        Route::get('create-language', [LanguageController::class, 'createLanguage'])->name('create.language')->middleware(['auth']);
        Route::post('store-language', [LanguageController::class, 'storeLanguage'])->name('store.language')->middleware(['auth']);
        Route::delete('lang/{lang}', [LanguageController::class, 'destroyLanguage'])->name('lang.destroy')->middleware(['auth']);

        // POLL
        Route::get('poll/fill/{id}', [PollController::class, 'poll'])->name('poll.fill')->middleware(['auth']);
        Route::post('poll/store/{id}', [PollController::class, 'fillStore'])->name('fill.poll.store');
        Route::post('image/poll/store/{id}', [PollController::class, 'imageStore'])->name('image.poll.store');
        Route::post('meeting/poll/store/{id}', [PollController::class, 'meetingStore'])->name('meeting.poll.store');

        Route::get('poll/image/fill/{id}', [PollController::class, 'imagePoll'])->name('image.poll.fill')->middleware(['auth']);
        Route::get('poll/meeting/fill/{id}', [PollController::class, 'meetingPoll'])->name('meeting.poll.fill')->middleware(['auth']);

        Route::get('poll/result/{id}', [PollController::class, 'pollResult'])->name('poll.result')->middleware(['auth']);
        Route::get('poll/image/result/{id}', [PollController::class, 'pollImageResult'])->name('poll.image.result')->middleware(['auth']);
        Route::get('poll/meeting/result/{id}', [PollController::class, 'pollMeetingResult'])->name('poll.meeting.result')->middleware(['auth']);

        Route::get('poll/survey/{id}', [PollController::class, 'publicFill'])->name('poll.survey');
        Route::get('poll/survey/meeting/{id}', [PollController::class, 'publicFillMeeting'])->name('poll.survey.meeting');
        Route::get('poll/survey/image/{id}', [PollController::class, 'publicFillImage'])->name('poll.survey.image');
        Route::get('poll/share/{id}', [PollController::class, 'share'])->name('poll.share');
        Route::get('qr/share/{id}', [PollController::class, 'shareQr'])->name('poll.share.qr');

        Route::get('poll/share/image/{id}', [PollController::class, 'shareImage'])->name('poll.share.image');
        Route::get('qr/share/image/{id}', [PollController::class, 'shareQrImage'])->name('poll.share.qr.image');

        Route::get('poll/share/meeting/{id}', [PollController::class, 'shareMeeting'])->name('poll.share.meeting');
        Route::get('qr/share/meeting/{id}', [PollController::class, 'shareQrMeeting'])->name('poll.share.qr.meeting');

        Route::get('poll/shares/{id}', [PollController::class, 'shares'])->name('poll.shares');
        Route::get('poll/shares/meetings/{id}', [PollController::class, 'shareMeetings'])->name('poll.shares.meetings');
        Route::get('poll/shares/images/{id}', [PollController::class, 'shareImages'])->name('poll.shares.images');
        Route::get('poll/public/result/{id}', [PollController::class, 'publicFillResult'])->name('poll.public.result');
        Route::get('meeting/public/result/{id}', [PollController::class, 'publicFillResultMeeting'])->name('poll.public.result.meeting');
        Route::get('image/public/result/{id}', [PollController::class, 'publicFillResultImage'])->name('poll.public.result.image');

        // cookie
        Route::get('cookie/consent', [SettingsController::class, 'cookieConsent'])->name('cookie.consent');
        //Cache
        Route::any('config-cache', function () {
            Artisan::call('cache:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            Artisan::call('optimize:clear');
            return redirect()->back()->with('success', __('Cache clear successfully.'));
        })->name('config.cache');
    }
);

Route::group(['middleware' => ['Setting', 'Upload', 'xss']], function () {
    Route::post('contact-mail', [RequestuserController::class, 'contactMail'])->name('contact.mail');
    Route::get('apply-coupon', [CouponController::class, 'applyCoupon'])->name('apply.coupon');

    // STRIPE
    Route::post('pre-stripe/pending', [RequestuserController::class, 'stripePostPending'])->name('pre.stripe.pending');
    Route::get('pre-payment-success/{id}', [RequestuserController::class, 'prePaymentSuccess'])->name('pre.stripe.success.pay');
    Route::get('pre-payment-cancel/{id}', [RequestuserController::class, 'prePaymentCancel'])->name('pre.stripe.cancel.pay');
    Route::post('pre-stripe', [RequestuserController::class, 'preStripeSession'])->name('pre.stripe.session');

    // RAZORPAY
    Route::post('paysrazorpay/payment', [RequestuserController::class, 'paysRazorPayPayment'])->name('paysrazorpay.payment');
    Route::get('paysrazorpay/callback/{order_id}/{transaction_id}/{requestuser_id}/{coupon_id}', [RequestuserController::class, 'paysRazorPayCallback']);

    // FLUTTEWAVE
    Route::post('paysflutterwave/payment', [RequestuserController::class, 'paysFlutterwavePayment'])->name('paysflutterwave.payment');
    Route::get('paysflutterwave/callback/{order_id}/{transaction_id}/{requestuser_id}/{coupon_id}', [RequestuserController::class, 'paysFlutterwaveCallback']);
    Route::post('process-transactions', [RequestuserController::class, 'processTransaction'])->name('processTransaction');
    Route::get('success-transactions/{data}', [RequestuserController::class, 'successTransaction'])->name('successTransaction');
    Route::get('cancel-transactions/{data}', [RequestuserController::class, 'cancelTransaction'])->name('cancelTransaction');

    // COINGATE
    Route::post('coingate/payment', [RequestuserController::class, 'coingatePayment'])->name('coingate.payment');
    Route::get('coingate-payment/{id}', [RequestuserController::class, 'coingatePlanGetPayment'])->name('coingatecallback');

    // PAYSTACK
    Route::post('paymentpaystack/payment', [RequestuserController::class, 'paymentPaystackPayment'])->name('paymentpaystack.payment');
    Route::get('paymentpaystack/callback/{order_id}/{transaction_id}/{requestuser_id}/{coupon_id}', [RequestuserController::class, 'paymentPaystackCallback']);

    // CASHFREE
    Route::post('cashfree/payment/prepare', [RequestuserController::class, 'cashfreePayment'])->name('cashfree.prepare');
    Route::get('cashfree/payment/callback', [RequestuserController::class, 'cashfreeCallback'])->name('cashfree.callback');

    // SSPAY
    Route::post('sspay/payment/init', [RequestuserController::class, 'sspayInitPayment'])->name('sspay.init');
    Route::get('sspay/payment/callback', [RequestuserController::class, 'sspayCallback'])->name('sspay.callback');

    // PAYUMONEY
    Route::post('payumoney/front/payment', [RequestuserController::class, 'frontPayUmoneyPayment'])->name('front.payumoney.payment.init');
    Route::any('payumoney/front/success/{id}', [RequestuserController::class, 'frontPayuSuccess'])->name('front.payu.success');
    Route::any('payumoney/front/failure/{id}', [RequestuserController::class, 'frontPayuFailure'])->name('front.payu.failure');

    // PAYTAB
    Route::post('plan-pay-with-paytab', [RequestuserController::class, 'planPayWithPaytab'])->name('plan.pay.with.paytab');
    Route::any('paytab-success/plan', [RequestuserController::class, 'paytabGetPayment'])->name('plan.paytab.success');

    //bkash
    Route::post('bkash/payment/token', [RequestuserController::class, 'token'])->name('bkash.payment.token');
    Route::post('bkash/payment/pay/init', [RequestuserController::class, 'paymentInit'])->name('bkash.pay.payment.init');
    Route::get('bkash/payment/createpayment', [RequestuserController::class, 'createPayment'])->name('bkash.payment.createpayment');
    Route::get('bkash/payment/executepayment', [RequestuserController::class, 'executePayment'])->name('bkash.payment.executepayment');

    // MERCADO
    Route::post('mercadopago/payment', [RequestuserController::class, 'mercadoPagoPayment'])->name('mercadopago.payment');
    Route::any('mercadopago-callback/{id}', [RequestuserController::class, 'mercadoPagoPaymentCallback'])->name('mercado.callback');

    // PAYTM
    Route::post('payment', [RequestuserController::class, 'pay'])->name('paytm.payment');
    Route::post('payment/callback', [RequestuserController::class, 'paymentCallback'])->name('paytm.callback');

    // OFFLINE
    Route::post('offline-paysuccess', [RequestuserController::class, 'offlinePaymentEntry'])->name('offline.payment.entry');

    //change status
    Route::post('coupon/status/{id}', [CouponController::class, 'couponStatus'])->name('coupon.status');
    Route::post('plan/status/{id}', [PlanController::class, 'planStatus'])->name('plan.status');
    Route::post('form/status/{id}', [FormController::class, 'formStatus'])->name('form.status');
});


// Announcement
Route::get('show-public/announcement/{slug}', [AnnouncementController::class, 'showPublicAnnouncement'])->name('show.public.announcement');
Route::group(['middleware' => ['auth', 'Setting', '2fa', 'Upload']], function () {
    Route::resource('announcement', AnnouncementController::class);
    Route::post('announcement-status/{id}', [AnnouncementController::class, 'announcementStatus'])->name('announcement.status');

    Route::get('show-announcement-list/', [AnnouncementController::class, 'showAnnouncementList'])->name('show.announcement.list');
    Route::get('show-announcement/{id}', [AnnouncementController::class, 'showAnnouncement'])->name('show.announcement');
});



//Blogs pages
Route::get('blogs/{slug}', [BlogController::class, 'viewBlog'])->name('view.blog');
Route::get('view/blogs', [BlogController::class, 'seeAllBlogs'])->name('see.all.blogs');

// FORM
Route::get('design/{id}', [FormController::class, 'design'])->name('forms.design')->middleware(['auth', 'Upload', 'xss']);
Route::put('forms/design/{id}', [FormController::class, 'designUpdate'])->name('forms.design.update')->middleware(['auth', 'Setting', 'Upload', 'xss']);
Route::get('forms/fill/{id}', [FormController::class, 'fill'])->name('forms.fill')->middleware(['auth', 'Setting', 'Upload', 'xss']);
Route::get('forms/survey/{id}', [FormController::class, 'publicFill'])->name('forms.survey')->middleware(['xss']);
Route::get('forms/qr/{id}', [FormController::class, 'qrCode'])->name('forms.survey.qr');
Route::put('forms/fill/{id}', [FormController::class, 'fillStore'])->name('forms.fill.store')->middleware(['xss', 'Setting', 'Upload']);
Route::get('form-values/{id}/edit', [FormValueController::class, 'edit'])->name('edit.form.values')->middleware(['auth', 'Setting', 'Upload', 'xss']);
Route::get('form-values/{id}/view', [FormValueController::class, 'showSubmitedForms'])->name('view.form.values')->middleware(['auth', 'Setting', 'Upload', 'xss']);
Route::post('form-duplicate', [FormController::class, 'duplicate'])->name('forms.duplicate')->middleware(['auth', 'Setting', 'Upload', 'xss']);
Route::get('form-values/{id}/download/csv2', [FormValueController::class, 'downloadCsv2'])->name('download.form.values.csv2')->middleware(['auth', 'Setting', 'Upload', 'xss']);
Route::post('form-values/excel', [FormValueController::class, 'exportXlsx'])->name('download.form.values.excel')->middleware(['auth', 'Setting', 'Upload', 'xss']);
Route::get('form-fill-edit-lock/{id}', [FormValueController::class, 'formFillEditlock'])->name('form.fill.edit.lock')->middleware(['auth', 'Setting', 'xss']);
Route::get('payment-slip-download/{id}', [FormValueController::class, 'PaymentSlipDownload'])->name('payment.slip.download')->middleware(['xss']);

// Conditional Logic
Route::get('rules/{id}', [FormController::class, 'formRules'])->name('form.rules');
Route::post('rule/store', [FormController::class, 'storeRule'])->name('rule.store');
Route::get('rule/{id}/edit', [FormController::class, 'editRule'])->name('rule.edit');
Route::patch('rule/{id}/update', [FormController::class, 'ruleUpdate'])->name('rule.update');
Route::delete('rule/{id}/delete', [FormController::class, 'ruleDelete'])->name('rule.delete');
Route::get('get/rules', [FormController::class, 'getField'])->name('get.field');


//Booking
Route::get('bookings/survey/time-wise/{id}', [BookingController::class, 'publicTimeFill'])->name('booking.survey.time.wise')->middleware(['xss', 'Upload']);
Route::get('bookings/survey/seats-wise/{id}', [BookingController::class, 'publicSeatFill'])->name('booking.survey.seats.wise')->middleware(['xss', 'Upload']);
Route::get('bookings/qr/{id}', [BookingController::class, 'qrCode'])->name('booking.survey.qr');
Route::get('bookings/appoinment/edit/{id}', [BookingValueController::class, 'editAppoinment'])->name('appointment.edit')->middleware('Upload');
Route::delete('bookings/appoinment/slots-cancel/{id}', [BookingValueController::class, 'slotCancel'])->name('appointment.slot.cancel')->middleware('Upload');
Route::delete('bookings/appoinment/seats-cancel/{id}', [BookingValueController::class, 'seatCancel'])->name('appointment.seat.cancel')->middleware('Upload');

//appoinment time
Route::post('bookings/slots/appoinment/get/{id}', [BookingController::class, 'getAppoinmentSlot'])->name('booking.slots.appoinment.get')->middleware(['xss', 'Setting', 'Upload']);
Route::post('bookings/seats/slot/appoinment/get/{id}', [BookingController::class, 'getAppoinmentSeat'])->name('booking.seats.slot.appoinment.get')->middleware(['xss', 'Setting', 'Upload']);
Route::post('bookings/seats/seat/appoinment/get/{id}', [BookingController::class, 'getAppoinmentSeatSeat'])->name('booking.seats.seat.appoinment.get')->middleware(['xss', 'Setting', 'Upload']);
Route::put('bookings/fill/{id}', [BookingController::class, 'fillStore'])->name('booking.fill.store')->middleware(['xss', 'Setting', 'Upload']);

Route::post('ckeditors/upload', [FormController::class, 'ckUpload'])->name('ckeditors.upload')->middleware('auth', 'Upload');
Route::post('dropzone/upload/{id}', [FormController::class, 'dropzone'])->name('dropzone.upload')->middleware(['Setting', 'Upload']);
Route::get('forms/grid/{id?}', [FormController::class, 'gridView'])->name('grid.form.view')->middleware(['auth', 'Setting', 'Upload', 'xss']);
Route::post('form-status/{id}', [FormController::class, 'formStatus'])->name('form.status')->middleware(['auth', 'Setting', 'Upload', 'xss']);
Route::post('ckeditor/upload', [FormController::class, 'upload'])->name('ckeditor.upload')->middleware('Upload');

Route::get('user/forms/survey/{id}', [HomeController::class, 'userFormQrcode'])->name('users.all.formsSurvey')->middleware('Upload');
Route::post('files/video/store', [FormValueController::class, 'videoStore'])->name('videostore')->middleware('Upload');
Route::get('download-image/{id}', [FormValueController::class, 'selfieDownload'])->name('selfie.image.download')->middleware('Upload');

// Form Coingate
Route::post('coingate/fill/prepare', [PaymentController::class, 'coingateFillPaymentPrepare'])->name('coingatefillprepare');
Route::get('coingate-fill-payment/{id}', [PaymentController::class, 'coingateFillPlanGetPayment'])->name('coingatefillcallback');

// Form Payumoney
Route::post('payumoney/fill/prepare', [PaymentController::class, 'payUmoneyFillPaymentPrepare'])->name('payumoneyfillprepare');
Route::any('payumoney-fill-payment', [PaymentController::class, 'payUmoneyFillPlanGetPayment'])->name('payumoneyfillcallback');

// form mercado
Route::post('mercado/fill/prepare', [PaymentController::class, 'mercadoFillPaymentPrepare'])->name('mercadofillprepare');
Route::get('mercado-fill-payment/{id}', [PaymentController::class, 'mercadoFillPlanGetPayment'])->name('mercadofillcallback');

// form paytm
Route::post('paytm-payment', [PaymentController::class, 'paymentPaytmPayment'])->name('paymentpaytm.payment')->middleware(['Setting', 'Upload']);
Route::post('paytm-callback', [PaymentController::class, 'paymentPaytmCallback'])->name('paymentpaytm.callbacks')->middleware(['Setting', 'Upload']);
Route::post('fillcallback', [PaymentController::class, 'paymentFillCallback'])->name('paymentfillcallback')->middleware(['Setting', 'Upload']);

Route::post('form-comment/store', [FormCommentsController::class, 'store'])->name('form.comment.store')->middleware(['xss', 'Upload']);
Route::delete('form-comment/destroy/{id}', [FormCommentsController::class, 'destroy'])->name('form.comment.destroy')->middleware(['xss', 'Upload']);
Route::post('form-comment/reply/store', [FormCommentsReplyController::class, 'store'])->name('form.comment.reply.store')->middleware(['xss', 'Upload']);
Route::post('comment/store', [CommentsController::class, 'store'])->name('comment.store')->middleware(['xss', 'Upload']);
Route::delete('comment/destroy/{id}', [CommentsController::class, 'destroy'])->name('comment.destroy')->middleware(['xss', 'Upload']);
Route::post('comment/reply/store', [CommentsReplyController::class, 'store'])->name('comment.reply.store')->middleware(['xss', 'Upload']);

// Impersonate
Route::impersonate();
Route::get('users/{id}/impersonate', [UserController::class, 'impersonate'])->name('users.impersonate');
Route::get('impersonate/leave', [UserController::class, 'leaveImpersonate'])->name('impersonate.leave');

//document
Route::post('document/design-menu/{id}', [DocumentGenratorController::class, 'documentDesignMenu'])->name('document.design.menu')->middleware(['auth', 'verified', 'Upload', '2fa', 'verified_phone']);
Route::post('document/status/{id}', [DocumentGenratorController::class, 'documentGenStatus'])->name('document.status')->middleware(['Upload']);

// public document
Route::get('document/public/{slug}', [DocumentGenratorController::class, 'documentPublic'])->name('document.public')->middleware(['xss', 'Upload']);
Route::get('documents/{slug}/{changelog?}', [DocumentGenratorController::class, 'documentPublicMenu'])->name('documentmenu.menu')->middleware(['xss', 'Upload']);
Route::get('document/{slug}/{slugmenu}', [DocumentGenratorController::class, 'documentPublicSubMenu'])->name('documentsubmenu.submenu')->middleware(['xss', 'Upload']);

Route::get('redirect/{provider}', [SocialLoginController::class, 'redirect'])->middleware(['Setting', 'Upload']);
Route::get('callback/{provider}', [SocialLoginController::class, 'callback'])->name('social.callback')->middleware(['Setting', 'Upload']);
Route::get('/', [HomeController::class, 'landingPage'])->name('landingpage')->middleware(['Setting', 'Upload']);
Route::get('changeLang/{lang?}', [HomeController::class, 'changeLang'])->name('change.lang');

//  Footer page
Route::get('pages/{slug}', [LandingPageController::class, 'pagesView'])->name('description.page')->middleware(['Upload']);
Route::get('contact/us', [RequestuserController::class, 'contactus'])->name('contactus')->middleware(['Upload']);
Route::get('all/faqs', [RequestuserController::class, 'faqs'])->name('faqs.pages')->middleware(['Upload']);

Route::post('register/smsindex/verify', function () {
    return redirect(URL()->previous());
})->name('2faVerify')->middleware('2fa');


Route::post('2fa', function () {
    return redirect(URL()->previous());
})->name('2fa')->middleware('2fa');

Route::group(['prefix' => '2fa'], function () {
    Route::get('/', [LoginSecurityController::class, 'show2faForm']);
    Route::post('generateSecret', [LoginSecurityController::class, 'generate2faSecret'])->name('generate2faSecret');
    Route::post('enable2fa', [LoginSecurityController::class, 'enable2fa'])->name('enable2fa');
    Route::post('disable2fa', [LoginSecurityController::class, 'disable2fa'])->name('disable2fa');
    Route::post('2faVerify', function () {
        return redirect()->route('home');
    })->name('2faVerify')->middleware('2fa');
});
