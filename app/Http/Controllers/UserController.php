<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\DataTables\UsersDataTable;
use App\Facades\UtilityFacades;
use App\Models\Plan;
use App\Models\RequestUser;
use App\Models\settings;
use App\Models\SocialLogin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\MailTemplates\Models\MailTemplate;
use App\Mail\RegisterMail;
use App\Models\NotificationsSetting;
use App\Notifications\RegisterMail as NotificationsRegisterMail;
use Lab404\Impersonate\Impersonate;

class UserController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:manage-user|create-user|edit-user|delete-user', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-user', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-user', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-user', ['only' => ['destroy']]);
    }

    public function index(UsersDataTable $dataTable)
    {
        if (\Auth::user()->users_grid_view == 1) {
            return redirect()->route('grid.view', 'view');
        }
        return $dataTable->render('users.index');
    }

    public function create()
    {
        if (Auth::user()->type == 'Super Admin') {
            $roles      = Role::pluck('name', 'id');
        } else {
            $role       = Role::select('id', 'name', 'created_by')->where('name', '!=', 'Super Admin')->where('name', '!=', 'Admin')->where('created_by', Auth::user()->admin_id)->get();
            $roles      = [];
            $roles['']  = __('Select role');
            foreach ($role as $value) {
                $roles[$value->id] = $value->name;
            }
        }
        $view = view('users.create', compact('roles'));
        return ['html' => $view->render()];
    }

    public function store(Request $request)
    {
        if (\Auth::user()->type == 'Super Admin') {
            request()->validate([
                'name'                      => 'required|string|max:191',
                'email'                     => 'required|email|unique:users,email',
                'password'                  => 'required|string|same:confirm-password',
                'phone'                     => 'required|unique:users,phone',
                'country_code'              => 'required|string|max:191',
            ]);
            $countries                      = \App\Core\Data::getCountriesList();
            $countryCode                    = $countries[$request->country_code]['phone_code'];
            $input                          = $request->all();
            $input['password']              = Hash::make($input['password']);
            $input['created_by']            = Auth::user()->admin_id;
            $input['lang']                  = UtilityFacades::getsettings('default_language');
            $input['type']                  = 'Admin';
            $input['plan_id']               = '1';
            $input['active_status']         = '1';
            $input['country_code']          = $countryCode;
            $input['phone']                 = $input['phone'];
            $input['avatar']                = 'avatar/avatar.png';
            $input['email_verified_at']     = (UtilityFacades::getsettings('email_verification') == '1') ? null : Carbon::now()->toDateTimeString();
            $input['phone_verified_at']     = (UtilityFacades::getsettings('sms_verification') == '1') ? null : Carbon::now()->toDateTimeString();
            $input['lang']                  = 'en';
            $user                           = User::create($input);
            $user->assignRole('Admin');
            if (UtilityFacades::getsettings('email_setting_enable') == 'on') {
                if (UtilityFacades::getsettings('mail_host') == true) {
                    if (MailTemplate::where('mailable', RegisterMail::class)->first()) {
                        try {
                            Mail::to($request->email)->send(new RegisterMail($request));
                        } catch (\Exception $e) {
                            return redirect()->back()->with('errors', $e->getMessage());
                        }
                    }
                }
            }
            $superAdmin     = User::where('type', 'Super Admin')->first();
            $notify         = NotificationsSetting::where('title', 'Register mail')->first();
            if (UtilityFacades::getsettings('email_setting_enable') == 'on') {
                if (isset($notify)) {
                    if ($notify->notify == '1') {
                        $superAdmin->notify(new NotificationsRegisterMail($user));
                    }
                }
            }

            $message = "Welcome" . env('APP_NAME') . "<br/>";
            $message .= "
                         <b>Dear </b> $request->name <br/>
                         <b>You are added in our app
                         <p> Your login Details:</p>
                         </b> $request->email<br/>";
            $settings = [
                ['key' => 'date_format', 'value' => 'M j, Y', 'created_by' => $user->id],
                ['key' => 'time_format', 'value' => 'g:i A', 'created_by' => $user->id],
                ['key' => 'color', 'value' => 'theme-2', 'created_by' => $user->id],
                ['key' => 'transparent_layout', 'value' => 'on', 'created_by' => $user->id],
            ];
            foreach ($settings as $setting) {
                settings::create($setting);
            }
        } elseif (\Auth::user()->type == 'Admin') {
            request()->validate(
                [
                    'name'                      => 'required|string|max:191',
                    'email'                     => 'required|email|unique:users,email',
                    'password'                  => 'required|string|same:confirm-password',
                    'phone'                     => 'required|unique:users,phone',
                    'country_code'              => 'required|string|max:191',
                    'roles'                     => 'required',
                ]
            );
            $role   = Role::findById($request->roles);
            $user   = User::find(\Auth::user()->admin_id);
            $users  = User::where('created_by', \Auth::user()->admin_id)->count();
            $plan   = Plan::find($user->plan_id);
            if ($users < $plan->max_users) {
                $countries                      = \App\Core\Data::getCountriesList();
                $countryCode                    = $countries[$request->country_code]['phone_code'];
                $input                          = $request->all();
                $input['password']              = Hash::make($input['password']);
                $input['country_code']          = $countryCode;
                $input['phone']                 = $input['phone'];
                $input['created_by']            = Auth::user()->admin_id;
                $input['lang']                  = UtilityFacades::getsettings('default_language');
                $input['active_status']         = '1';
                $input['avatar']                = 'avatar/avatar.png';
                $input['type']                  = $role->name;
                $input['email_verified_at']     = (UtilityFacades::getsettings('email_verification') == '1') ? null : Carbon::now()->toDateTimeString();
                $input['phone_verified_at']     = (UtilityFacades::getsettings('sms_verification') == '1') ? null : Carbon::now()->toDateTimeString();
                $user                           = User::create($input);
                $user->assignRole($role->id);
                if (UtilityFacades::getsettings('email_verification') == '1') {
                    try {
                        $user->sendEmailVerificationNotification();
                    } catch (\Exception $th) {
                        return redirect()->back()->with('errors', $th->getMessage());
                    }
                }
                if (UtilityFacades::getsettings('email_setting_enable') == 'on') {
                    if (UtilityFacades::getsettings('mail_host') == true) {
                        if (MailTemplate::where('mailable', RegisterMail::class)->first()) {
                            try {
                                Mail::to($request->email)->send(new RegisterMail($request));
                            } catch (\Exception $e) {
                                return redirect()->back()->with('errors', $e->getMessage());
                            }
                        }
                    }
                }

                $Admin = User::where('type',  'Admin')->first();
                $notify = NotificationsSetting::where('title', 'Register mail')->first();
                if (UtilityFacades::getsettings('email_setting_enable') == 'on') {
                    if (isset($notify)) {
                        if ($notify->notify == '1') {
                            $Admin->notify(new NotificationsRegisterMail($user));
                        }
                    }
                }

                $message = "Welcome" . env('APP_NAME') . "<br/>";
                $message .= "
                            <b>Dear </b> $request->name <br/>
                            <b>You are added in our app
                            <p> Your login Details:</p>
                            </b> $request->email<br/>";
            } else {
                return redirect()->back()->with('failed', __('Your user limit is over, Please upgrade plan.'));
            }
        } else {
            request()->validate(
                [
                    'name'                      => 'required|string|max:191',
                    'email'                     => 'required|email|unique:users,email',
                    'password'                  => 'required|string|same:confirm-password',
                    'phone'                     => 'required|unique:users,phone',
                    'country_code'              => 'required|string|max:191',
                    'roles'                     => 'required',
                ]
            );
            $role                               = Role::findById($request->roles);
            $user                               = User::find(\Auth::user()->admin_id);
            $users                              = User::where('created_by', \Auth::user()->admin_id)->count();
            $plan                               = Plan::find($user->plan_id);
            if ($users < $plan->max_users) {
                $countries                      = \App\Core\Data::getCountriesList();
                $countryCode                    = $countries[$request->country_code]['phone_code'];
                $input                          = $request->all();
                $input['type']                  = $role->name;
                $input['password']              = Hash::make($input['password']);
                $input['country_code']          = $countryCode;
                $input['phone']                 = $input['phone'];
                $input['created_by']            = Auth::user()->admin_id;
                $input['active_status']         = '1';
                $input['active']                = 'avatar/avatar.png';
                $input['lang']                  = UtilityFacades::getsettings('default_language');
                $input['email_verified_at']     = (UtilityFacades::getsettings('email_verification') == '1') ? null : Carbon::now()->toDateTimeString();
                $input['phone_verified_at']     = (UtilityFacades::getsettings('sms_verification') == '1') ? null : Carbon::now()->toDateTimeString();
                $user                           = User::create($input);
                if (UtilityFacades::getsettings('email_verification') == '1') {
                    try {
                        $user->sendEmailVerificationNotification();
                    } catch (\Throwable $th) {
                        return redirect()->back()->with('errors', $th->getMessage());
                    }
                }
                if (UtilityFacades::getsettings('email_setting_enable') == 'on') {
                    if (UtilityFacades::getsettings('mail_host') == true) {
                        if (MailTemplate::where('mailable', RegisterMail::class)->first()) {
                            try {
                                Mail::to($request->email)->send(new RegisterMail($request));
                            } catch (\Exception $e) {
                                return redirect()->back()->with('errors', $e->getMessage());
                            }
                        }
                    }
                }
                $user->assignRole($role->id);
                $message = "Welcome" . env('APP_NAME') . "<br/>";
                $message .= "
                <b>Dear </b> $request->name <br/>
                <b>You are added in our app
                <p> Your login Details:</p>
                </b> $request->email<br/>";
            } else {
                return redirect()->back()->with('failed', __('Your user limit is over, Please upgrade plan.'));
            }
        }
        return redirect()->route('users.index')
            ->with('success',  __('User created successfully.'));
    }

    public function edit($id)
    {
        $user      = User::find($id);
        $role      = Role::where('created_by', auth()->user()->admin_id)->get();
        $roles     = [];
        $roles[''] = __('Select role');
        foreach ($role as $value) {
            $roles[$value->name] = $value->name;
        }
        $userRole   = $user->roles->pluck('name', 'name')->all();
        $view       = view('users.edit', compact('user', 'roles', 'userRole'));
        return ['html' => $view->render()];
    }

    public function update(Request $request, $id)
    {
        request()->validate([
            'name'          => 'required|string|max:191',
            'email'         => 'required|email|unique:users,email,' . $id,
            'password'      => 'string|same:confirm-password',
            'phone'         => 'required|unique:users,phone,' . $id,
            'country_code'  => 'required|string|max:191',
        ]);
        $countries          = \App\Core\Data::getCountriesList();
        $countryCode        = $countries[$request->country_code]['phone_code'];
        $input              = $request->all();
        if (!isset($input['password']) || $input['password'] != '') {
            $input['password'] = Hash::make($input['password']);
        } else {
            unset($input['password']);
        }
        $user                   = User::find($id);
        $input['country_code']  = $countryCode;
        $input['phone']         = $input['phone'];
        $user->update($input);
        return redirect()->route('users.index')
            ->with('success',  __('User updated successfully.'));
    }

    public function destroy($id)
    {
        if (\Auth::user()->can('delete-user')) {
            if ($id != 1) {
                $user           = User::find($id);
                User::where('created_by', $id)->delete();
                $requestUser    = RequestUser::where('email', $user->email)->first();
                $socialLogin    = SocialLogin::where('user_id', $id)->get();
                if ($user->type == 'Admin') {
                    if ($requestUser) {
                        $requestUser->delete();
                    }
                }
                foreach ($socialLogin as $value) {
                    if ($user->type == 'Admin') {
                        if ($value) {
                            $value->delete();
                        }
                    }
                }
                $user->delete();
                return redirect()->back()->with('success', __('User deleted successfully.'));
            } else {
                return redirect()->back()->with('failed', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function profile($id)
    {
        $user = User::find($id);
        return view('users.profile', compact('user'));
    }

    public function accountStatus($id)
    {
        $user = User::find($id);
        if ($user->active_status == 1) {
            $user->active_status = 0;
            $user->save();
            return redirect()->back()->with('success', __('User deactiveted successfully.'));
        } else {
            $user->active_status = 1;
            $user->save();
            return redirect()->back()->with('success', __('User activeted successfully.'));
        }
    }

    public function useremailverified($id)
    {
        $user = User::find($id);
        if ($user->email_verified_at) {
            $user->email_verified_at = null;
            $user->save();
            return redirect()->back()->with('success', __('User unverified successfully.'));
        } else {
            $user->email_verified_at = Carbon::now();
            $user->save();
            return redirect()->back()->with('success', __('User verified successfully.'));
        }
    }

    public function userphoneverified($id)
    {
        $user = User::find($id);
        if ($user->phone_verified_at) {
            $user->phone_verified_at = null;
            $user->save();
            return redirect()->back()->with('success', 'User phone unverification successfully.');
        } else {
            $user->phone_verified_at = Carbon::now();
            $user->save();
            return redirect()->back()->with('success', 'User phone verification successfully.');
        }
    }

    public function userStatus(Request $request, $id)
    {
        $users = User::find($id);
        $input = ($request->value == "true") ? 1 : 0;
        if ($users) {
            $users->active_status = $input;
            $users->save();
        }
        return response()->json(['is_success' => true, 'message' => __('User status changed successfully.')]);
    }

    public function impersonate(Request $request, User $user,  $id)
    {
        $user = User::find($id);
        if ($user && auth()->check()) {
            Impersonate::take($request->user(), $user);
            return redirect('/home');
        }
    }

    public function leaveImpersonate(Request $request)
    {
        \Auth::user()->leaveImpersonation($request->user());
        return redirect('/home');
    }

    public function gridView($slug = '')
    {
        $user                   = \Auth::user();
        $user->users_grid_view  = ($slug) ? 1 : 0;
        $user->save();
        if ($user->users_grid_view == 0) {
            return redirect()->route('users.index');
        }
        if ($user->type == 'Super Admin') {
            $users = User::select('plans.*', 'plans.name as plan_name', 'users.*')->where('users.type', '!=', 'Super Admin')->where('users.created_by', $user->admin_id)->get();
        } else {
            $users = User::where('users.type', '!=', 'Super Admin')->where('users.created_by', $user->admin_id)->get();
        }
        return view('users.grid-view', compact('users'));
    }

    public function userPlanAssign($user_id, $plan_id)
    {
        if (\Auth::user()->can('plan-upgrade-user')) {
            $user               = User::find($user_id);
            $plan               = Plan::find($plan_id);
            $user->plan_id      = $plan->id;
            if ($plan->durationtype == 'Month' && $plan->id != '1') {
                $user->plan_expired_date = Carbon::now()->addMonths($plan->duration)->isoFormat('YYYY-MM-DD');
            } elseif ($plan->durationtype == 'Year' && $plan->id != '1') {
                $user->plan_expired_date = Carbon::now()->addYears($plan->duration)->isoFormat('YYYY-MM-DD');
            } else {
                $user->plan_expired_date = null;
            }
            $user->save();
            return redirect()->back()->with('success', __('Plan successfully upgraded.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function userPlan($id)
    {
        $user   = User::find($id);
        $plans  = Plan::where('active_status', 1)->get();
        return view('users.plan', compact('plans', 'user'));
    }
}
