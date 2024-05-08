<?php

namespace App\Http\Controllers;

use App\DataTables\OfflineRequestDataTable;
use App\Facades\UtilityFacades;
use App\Mail\ApproveOfflineMail;
use App\Mail\OfflineMail;
use App\Models\Coupon;
use App\Models\OfflineRequest;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserCoupon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Spatie\MailTemplates\Models\MailTemplate;

class OfflineRequestController extends Controller
{
    public function index(OfflineRequestDataTable $dataTable)
    {
        return $dataTable->render('offline-request.index');
    }

    public function offlineRequestStatus($id)
    {
        $offline            = OfflineRequest::find($id);
        $user               = User::find($offline->user_id);
        $plan               = Plan::find($offline->plan_id);
        $order              = Order::find($offline->order_id);
        $coupons            = Coupon::find($offline->coupon_id);
        $user->plan_id      = $plan->id;
        if (!empty($coupons)) {
            $userCoupon         = new UserCoupon();
            $userCoupon->user   = $user->id;
            $userCoupon->coupon = $coupons->id;
            $userCoupon->order  = $order->id;
            $userCoupon->save();
            $usedCoupun = $coupons->usedCoupon();
            if ($coupons->limit <= $usedCoupun) {
                $coupons->is_active = 0;
                $coupons->save();
            }
        }
        if ($plan->durationtype == 'Month' && $plan->id != '1') {
            $user->plan_expired_date = Carbon::now()->addMonths($plan->duration)->isoFormat('YYYY-MM-DD');
        } elseif ($plan->durationtype == 'Year' && $plan->id != '1') {
            $user->plan_expired_date = Carbon::now()->addYears($plan->duration)->isoFormat('YYYY-MM-DD');
        } else {
            $user->plan_expired_date = null;
        }
        $user->save();
        $offline->is_approved   = 1;
        $offline->update();
        $order->status          = 1;
        $order->paymet_type     = 'offline';
        $order->update();
        $user = User::find($offline->user_id);
        if (UtilityFacades::getsettings('email_setting_enbale') == 'on') {
            if (MailTemplate::where('mailable', ApproveOfflineMail::class)->first()) {
                try {
                    Mail::to($offline->email)->send(new ApproveOfflineMail($plan, $user));
                } catch (\Exception $e) {
                    return redirect()->back()->with('errors', $e->getMessage());
                }
            }
        }
        return redirect()->back()->with('success',  __('Plan update request send successfully.'));
    }

    public function offlineDisapprove(Request $request, $id)
    {
        request()->validate([
            'disapprove_reason'             => 'required|string',
        ]);
        $offlineRequest                     = OfflineRequest::find($id);
        $offlineRequest->disapprove_reason  = $request->disapprove_reason;
        $offlineRequest->is_approved        = 2;
        $offlineRequest->update();
        $order                              = Order::find($offlineRequest->order_id);
        $order->status                      = 2;
        $order->paymet_type                 = 'offline';
        $order->update();
        $user                               = User::find($offlineRequest->user_id);
        if (UtilityFacades::getsettings('email_setting_enable') == 'on') {
            if (MailTemplate::where('mailable', OfflineMail::class)->first()) {
                try {
                    Mail::to($offlineRequest->email)->send(new OfflineMail($offlineRequest, $user));
                } catch (\Exception $e) {
                    return redirect()->back()->with('errors', $e->getMessage());
                }
            }
        }
        return redirect()->back()->with('success', __('Request disapprove successfully.'));
    }

    public function disapproveStatus($id)
    {
        $offlineRequest   = OfflineRequest::find($id);
        if ($offlineRequest->is_approved == 0) {
            $view         = view('offline-request.reason', compact('offlineRequest'));
            return ['html' => $view->render()];
        } else {
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        $offlineRequest = OfflineRequest::find($id);
        $offlineRequest->delete();
        return redirect()->back()->with('success', __('Offline request deleted successfully.'));
    }

    //offline coupon
    public function offlinePaymentEntry(Request $request)
    {
        if (Auth::user()->type == 'Admin') {
            $authuser           = \Auth::user();
            $planID             = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
            $plan               = Plan::find($planID);
            $couponId           = '0';
            $couponCode         = null;
            $discountValue      = null;
            $price              = $plan->price;
            $coupons            = Coupon::where('code', $request->coupon)->where('is_active', '1')->first();
            if ($coupons) {
                $couponCode     = $coupons->code;
                $usedCoupun     = $coupons->usedCoupon();
                if ($coupons->limit == $usedCoupun) {
                    $res_data['errors'] = 'This coupon code has expired.';
                } else {
                    $discount = $coupons->discount;
                    $discount_type  = $coupons->discount_type;
                    $discountValue  = UtilityFacades::calculateDiscount($price, $discount, $discount_type);
                    $price          = $price - $discountValue;
                    if ($price < 0) {
                        $price      = $plan->price;
                    }
                    $couponId       = $coupons->id;
                }
            }
            $data = Order::create([
                'plan_id'           => $plan->id,
                'user_id'           => $authuser->id,
                'amount'            => $price,
                'discount_amount'   => $discountValue,
                'coupon_code'       => $couponCode,
                'status'            => 0,
            ]);
            $res_data['total_price']    = $price;
            $res_data['coupon']         = $couponId;
            $res_data['order_id']       = $data->id;
            OfflineRequest::create([
                'order_id'      => $res_data['order_id'],
                'plan_id'       => $plan->id,
                'coupon_id'     => $res_data['coupon'],
                'user_id'       => $authuser->id,
                'email'         => $authuser->email,
            ]);
        } else {
            OfflineRequest::create([
                'order_id'      => $request->o_order_id,
                'plan_id'       => $request->o_plan_id,
                'user_id'       =>  Auth::user()->id,
                'email'         => Auth::user()->email,
            ]);
        }
        return redirect()->route('plans.index')
            ->with('success',  __('Plan update request send successfully.'));
    }
}
