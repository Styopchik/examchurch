<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Storage;
use Lab404\Impersonate\Models\Impersonate;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use HasRoles;
    use Impersonate;

    protected $fillable = [
        'id', 'name', 'email', 'password', 'type', 'profile', 'lang', 'created_by', 'plan_id', 'avatar',
        'social_type', 'email_verified_at', 'active_status', 'country', 'country_code', 'phone', 'phone_verified_at', 'theme_color', 'dark_layout', 'rtl_layout', 'transprent_layout', 'users_grid_view', 'forms_grid_view'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];

    public function loginSecurity()
    {
        return $this->hasOne('App\Models\LoginSecurity');
    }

    public function currentLanguage()
    {
        return $this->lang;
    }

    public function getAdminIdAttribute()
    {
        if ($this->type == 'Admin' || $this->type == 'Super Admin') {
            return $this->id;
        } else {
            return $this->created_by;
        }
    }

    public function plan()
    {
        return $this->hasOne(Plan::class, 'id', 'plan_id');
    }

    public function assignPlan($plan_id)
    {
        $this->plan_id = $plan_id;
        $this->save();
        $this->syncplanChanges($plan_id);
    }

    public function syncplanChanges($plan_id)
    {
        $max_users = 0;
        $usr  = $this;
        if ($this->plan->durationtype == 'Month' && $plan_id != '1') {
            $this->plan_expired_date = Carbon::now()->addMonths($this->plan->duration)->isoFormat('YYYY-MM-DD');
        } elseif ($this->plan->durationtype == 'Year' && $plan_id != '1') {
            $this->plan_expired_date = Carbon::now()->addYears($this->plan->duration)->isoFormat('YYYY-MM-DD');
        } else {
            $this->plan_expired_date = null;
        }
        if ($this->plan) {
            $max_users = $this->plan->max_users;
        }
        User::where('created_by', '=', $this->admin_id)->update(['active_status' => 0]);
        if ($max_users) {
            User::where('created_by', '=', $this->admin_id)->limit($max_users)->update(['active_status' => 1]);
        }
    }

    public function getAvatarImageAttribute()
    {
        $avatar = \File::exists($this->avatar) ? Storage::url($this->avatar) : Storage::url('avatar/avatar.png');
        return $avatar;
    }

    public function hasVerifiedPhone()
    {
        return !is_null($this->phone_verified_at);
    }

    public function lastCodeRemainingSeconds()
    {
        $temp = UserCode::where('user_id', '=', $this->id)->first();
        if (isset($temp)) {
            $seconds = $temp->updated_at->diffInSeconds(Carbon::now());
            if ($seconds > 60) {
                return 60;
            } else {
                return 60 - $seconds;
            }
        } else {
            return 60;
        }
    }
}
