<?php

namespace App\Http\Controllers;

use App\DataTables\SalesDataTable;
use App\Facades\UtilityFacades;
use App\Models\Announcement;
use App\Models\Blog;
use App\Models\DashboardWidget;
use App\Models\Faq;
use App\Models\FooterSetting;
use App\Models\Form;
use App\Models\FormValue;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Poll;
use App\Models\Role;
use App\Models\Testimonial;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Carbon\CarbonInterval;
use DatePeriod;
use Hashids\Hashids;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function landingPage()
    {
        if (!file_exists(storage_path() . "/installed")) {
            header('location:install');
            die;
        }
        $lang = UtilityFacades::getActiveLanguage();
        \App::setLocale($lang);
        if (UtilityFacades::getsettings('landing_page_status') == 1) {
            $forms                          = Form::where('assign_type', 'public')->get();
            $faqs                           = Faq::orderBy('order')->take(4)->get();
            $features                       = json_decode(UtilityFacades::getsettings('feature_setting'));
            $testimonials                   = Testimonial::where('status', 1)->get();
            $appsMultipleImageSettings      = json_decode(UtilityFacades::getsettings('apps_multiple_image_setting'));
            $footerMainMenus                = FooterSetting::where('parent_id', 0)->get();
            $businessGrowthsViewSettings    = json_decode(UtilityFacades::getsettings('business_growth_view_setting'));
            $businessGrowthsSettings        = json_decode(UtilityFacades::getsettings('business_growth_setting'));
            $blogs                          = Blog::all();
            $plans                          = Plan::where('active_status', 1)->get();
            $currentDate = now()->toDateString();
            $announcementLists = Announcement::where('status', '1')
            ->where('start_date', '<=', $currentDate)
            ->where('end_date', '>=', $currentDate)
            ->where('share_with_public', '1')
            ->get();
        $announcementBars = Announcement::where('status', '1')
            ->where('start_date', '<=', $currentDate)
            ->where('end_date', '>=', $currentDate)
            ->where('show_landing_page_announcebar', '1')
            ->get();
            return view('welcome', compact('announcementLists' , 'announcementBars', 'appsMultipleImageSettings', 'faqs', 'forms', 'testimonials', 'features', 'footerMainMenus', 'businessGrowthsViewSettings', 'businessGrowthsSettings', 'blogs', 'lang', 'plans'));
        } else {
            return redirect()->route('home');
        }
    }

    public function changeLang($lang = '')
    {
        if ($lang == '') {
            $lang = UtilityFacades::getActiveLanguage();
        }
        Cookie::queue('lang', $lang, 120);
        return redirect()->back()->with('success', __('Language changed successfully.'));
    }

    public function index()
    {
        if (!file_exists(storage_path() . "/installed")) {
            header('location:install');
            die;
        } else {
            $widgets            = DashboardWidget::orderBy('position')->get();
            $usr                = \Auth::user();
            $userId             = $usr->id;
            $roles              = Role::where('name', $usr->type)->first();
            $roleId             = $roles->id;
            if ($usr->type == 'Super Admin') {
                $user           = User::where('id', '!=', $userId)->count();
                $form           = Form::count();
                $earning        = Order::sum('amount');
                $poll           = Poll::count();
                $submittedForm  = FormValue::count();
            } else if ($usr->type == 'Admin') {
                $user           = User::where('created_by', $userId)->count();
                $form           = Form::where('created_by', $userId)->count();
                $poll           = Poll::where('created_by', $userId)->count();
                $earning        = Order::sum('amount');
                $submittedForm  = FormValue::where('user_id', $userId)->count();
            } else {
                $user           = User::where('created_by', $usr->admin_id)->count();
                $submittedForm  = FormValue::select(['form_values.*', 'forms.title'])
                    ->join('forms', 'forms.id', '=', 'form_values.form_id')
                    ->where(function ($query1) use ($roleId, $userId) {
                        $query1->whereIn('form_values.form_id', function ($query) use ($roleId) {
                            $query->select('form_id')->from('assign_forms_roles')->where('role_id', $roleId);
                        })
                            ->orWhereIn('form_values.form_id', function ($query) use ($userId) {
                                $query->select('form_id')->from('assign_forms_users')->where('user_id', $userId);
                            });
                    })->count();
                $form           = Form::where(function ($query) use ($roleId, $userId) {
                    $query->whereIn('id', function ($query1) use ($roleId) {
                        $query1->select('form_id')->from('assign_forms_roles')->where('role_id', $roleId);
                    })->OrWhereIn('id', function ($query1) use ($userId) {
                        $query1->select('form_id')->from('assign_forms_users')->where('user_id', $userId);
                    });
                })->count();
                $earning        = Order::sum('amount');
                $poll           = Poll::where('created_by', Auth::user()->admin_id)->count();
            }
            return view('dashboard.home', compact('user', 'form', 'submittedForm', 'earning', 'poll', 'widgets'));
        }
    }

    public function formChart(Request $request)
    {
        if (Auth::user()->type == 'Super Admin') {
            $chartLable     = [];
            $chartValue     = [];
            $startDate      = Carbon::parse($request->start);
            $endDate        = Carbon::parse($request->end);
            $monthsDiff     = $endDate->diffInMonths($startDate);
            if ($monthsDiff >= 0 && $monthsDiff < 3) {
                $endDate    = $endDate->addDay();
                $interval   = CarbonInterval::day();
                $timeType   = "date";
                $dateFormat = "DATE_FORMAT(created_at, '%Y-%m-%d')";
            } elseif ($monthsDiff >= 3 && $monthsDiff < 12) {
                $interval   = CarbonInterval::month();
                $timeType   = "month";
                $dateFormat = "DATE_FORMAT(created_at, '%Y-%m')";
            } else {
                $interval   = CarbonInterval::year();
                $timeType   = "year";
                $dateFormat = "YEAR(created_at)";
            }
            $userReaports = Order::select(DB::raw($dateFormat . ' AS ' . $timeType . ',COUNT(id) AS userCount'))
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->groupBy(DB::raw($dateFormat))
                ->get()
                ->toArray();
            $dateRange = new DatePeriod($startDate, $interval, $endDate);
            switch ($timeType) {
                case 'date':
                    $format         = 'Y-m-d';
                    $labelFormat    = 'd M';
                    break;
                case 'month':
                    $format         = 'Y-m';
                    $labelFormat    = 'M Y';
                    break;
                default:
                    $format         = 'Y';
                    $labelFormat    = 'Y';
                    break;
            }
            foreach ($dateRange as $date) {
                $foundReport = false;
                $orderDate = Carbon::parse($date->format('Y-m-d'));
                foreach ($userReaports as $orderReaport) {
                    if ($orderReaport[$timeType] == $date->format($format)) {
                        $chartLable[]       = $orderDate->format($labelFormat);
                        $chartValue[]       = $orderReaport['userCount'];
                        $foundReport        = true;
                        break;
                    }
                }
                if (!$foundReport) {
                    $chartLable[] = $orderDate->format($labelFormat);
                    $chartValue[] = 0.0;
                } else if (!$userReaports) {
                    $chartLable[] = $orderDate->format($labelFormat);
                    $chartValue[] = 0.0;
                }
            }
            return response()->json(['lable' => $chartLable, 'value' => $chartValue], 200);
        }
    }

    public function sales(SalesDataTable $dataTable)
    {
        if (Auth::user()->type == 'Super Admin') {
            return $dataTable->render('sales.index');
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }


    public function changeThememode()
    {
        $user = \Auth::user();
        if ($user->dark_layout == 1) {
            $user->dark_layout = 0;
        } else {
            $user->dark_layout = 1;
        }
        $user->save();
        return response()->json(['mode' => $user->dark_layout]);
    }

    public function readNotification()
    {
        auth()->user()->notifications->markAsRead();
        return response()->json(['is_success' => true], 200);
    }

    public function userFormQrcode($id)
    {
        $hashids    = new Hashids('', 20);
        $decodedId  = $hashids->decodeHex($id);
        $forms      = Form::where('created_by', $decodedId)->get();
        if ($forms) {
            return view('dashboard.users-forms', compact('forms'));
        } else {
            abort(404);
        }
    }
}
