<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\CouponDataTable;
use App\DataTables\UserCouponDatatable;
use App\Facades\UtilityFacades;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use App\Models\UserCoupon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    public function index(CouponDataTable $dataTable)
    {
        if (Auth::user()->type == 'Super Admin') {
            if (\Auth::user()->can('manage-coupon')) {
                $totalCoupon        = Coupon::count();
                $expieredCoupon     = Coupon::where('is_active', '0')->count();
                $totalUsedCoupon    = UserCoupon::count();
                $totalUseAmount     = Order::where('status', 1)->sum('discount_amount');
                return $dataTable->render('coupon.index', compact('totalCoupon', 'expieredCoupon', 'totalUsedCoupon', 'totalUseAmount'));
            } else {
                return redirect()->back()->with('failed', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (Auth::user()->type == 'Super Admin') {
            if (\Auth::user()->can('create-coupon')) {
                return view('coupon.create');
            } else {
                return redirect()->back()->with('failed', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('create-coupon')) {
            request()->validate([
                'icon_input' => 'required|string',
            ]);
            if ($request->icon_input == 'manual') {
                $request->merge(['code' => $request->manualCode]);
            } else {
                $request->merge(['code' => $request->autoCode]);
            }
            request()->validate([
                'discount'       => 'required|numeric',
                'discount_type'  => 'required|string|max:191',
                'limit'          => 'required|integer',
                'code'           => 'required|string|max:191|unique:coupons,code',
            ]);
            $coupon                     = new Coupon();
            $coupon->discount           = $request->discount;
            $coupon->discount_type      = $request->discount_type;
            $coupon->limit              = $request->limit;
            $coupon->code               = strtoupper($request->code);
            $coupon->save();
            return redirect()->route('coupon.index')->with('success', __('Coupon created successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function show(UserCouponDatatable $dataTable, $id)
    {
        return $dataTable->render('coupon.show');
    }

    public function edit($id)
    {
        if (Auth::user()->type == 'Super Admin') {
            if (\Auth::user()->can('edit-coupon')) {
                $coupon = Coupon::find($id);
                return view('coupon.edit', compact('coupon'));
            } else {
                return redirect()->back()->with('failed', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-coupon')) {
            request()->validate([
                'discount'       => 'required|numeric',
                'discount_type'  => 'required|string|max:191',
                'limit'          => 'required|integer',
                'code'           => 'required|string|max:191|unique:coupons,code,' . $id,
            ]);
            $coupon                     = Coupon::find($id);
            $coupon->discount           = $request->discount;
            $coupon->discount_type      = $request->discount_type;
            $coupon->limit              = $request->limit;
            $coupon->code               = strtoupper($request->code);
            $coupon->save();
            return redirect()->route('coupon.index')
                ->with('success',  __('Coupon updated successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->type == 'Super Admin') {
            if (\Auth::user()->can('delete-coupon')) {
                $coupon = Coupon::find($id);
                $coupon->delete();
                return redirect()->route('coupon.index')
                    ->with('success',  __('Coupon deleted successfully.'));
            } else {
                return redirect()->back()->with('failed', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function uploadCsv()
    {
        return view('coupon.upload-coupon');
    }

    public function uploadCsvStore(Request $request)
    {
        request()->validate([
            'file'                          => 'required|file|mimes:csv'
        ]);
        try {
            if ($request->hasFile('file')) {
                $file                       = $request->file;
                $fileName                   = time() . '.' . $file->extension();
                $path                       = $file->storeAs('/coupon', $fileName);
                $couponData                 = array_map('str_getcsv', file(Storage::path($path)));
                array_shift($couponData);
                foreach ($couponData as $couponValue) {
                    $coupon                 = new Coupon();
                    $coupon->discount_type  = $couponValue[0];
                    $coupon->code           = $couponValue[1];
                    $coupon->discount       = $couponValue[2];
                    $coupon->limit          = $couponValue[3];
                    $coupon->is_active      = 1;
                    $coupon->save();
                }
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('errors', __('File uploaded successfully.'));
        }
        return redirect()->route('coupon.index')->with('success',  __('Coupon created successfully.'));
    }

    public function massCreate()
    {
        if (Auth::user()->type == 'Super Admin') {
            return view('coupon.mass-create');
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function massCreateStore(Request $request)
    {
        request()->validate([
            'discount'          => 'required|numeric',
            'discount_type'     => 'required|string|max:191',
            'mass_create'       => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    if ($value > 50) {
                        $fail("The mass create may not be greater than 50.");
                    }
                },
            ],
            'limit'             => 'required|integer',
        ]);
        $massCreate                     = $request->mass_create;
        for ($i = 1; $i <= $massCreate; $i++) {
            $coupon                     = new Coupon();
            $coupon->discount           = $request->discount;
            $coupon->discount_type      = $request->discount_type;
            $coupon->limit              = $request->limit;
            $coupon->code               = strtoupper(Str::random(10));
            $coupon->save();
        }
        return redirect()->route('coupon.index')->with('success', __('Coupon created successfully.'));
    }

    public function applyCoupon(Request $request)
    {
        $plan               = Plan::find(\Illuminate\Support\Facades\Crypt::decrypt($request->plan_id));
        if ($plan && $request->coupon != '') {
            $originalPrice  = UtilityFacades::amountFormat($plan->price);
            $coupons        = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
            if (!empty($coupons)) {
                $usedCoupun = $coupons->usedCoupon();
                if ($coupons->limit == $usedCoupun) {
                    return response()->json(
                        [
                            'is_success'    => false,
                            'final_price'   => $originalPrice,
                            'price'         => number_format($plan->price, 2),
                            'message'       => __('This coupon code has expired.'),
                        ]
                    );
                } else {
                    if ($coupons->discount_type == 'discount') {
                        $discountValue      = ($plan->price / 100) * $coupons->discount;
                    } else {
                        $discountValue      = $coupons->discount;
                    }
                    $planPrice              = $plan->price - $discountValue;
                    $price                  = UtilityFacades::amountFormat($plan->price - $discountValue);
                    $discountValue          = '-' . UtilityFacades::amountFormat($discountValue);
                    if ($planPrice < 0) {
                        return response()->json([
                            'is_success'        => false,
                            'discount_price'    => UtilityFacades::amountFormat(0),
                            'currency_symbol'   => env('CURRENCY_SYMBOL'),
                            'final_price'       => UtilityFacades::amountFormat($plan->price),
                            'price'             => number_format($plan->price, 2),
                            'message'           => __('Price is negative please enter currect coupon code.'),
                        ]);
                    } else {
                        return response()->json([
                            'is_success'        => true,
                            'discount_price'    => $discountValue,
                            'currency_symbol'   => env('CURRENCY_SYMBOL'),
                            'final_price'       => $price,
                            'price'             => number_format($planPrice, 2),
                            'message'           => __('Coupon code has applied successfully.'),
                        ]);
                    }
                }
            } else {
                return response()->json([
                    'is_success'                => false,
                    'final_price'               => $originalPrice,
                    'price'                     => number_format($plan->price, 2),
                    'message'                   => __('This coupon code is invalid or has expired.'),
                ]);
            }
        }
    }

    public function couponStatus(Request $request,  $id)
    {
        $coupon = Coupon::find($id);
        $input = ($request->value == "true") ? 1 : 0;
        if ($coupon) {
            $coupon->is_active = $input;
            $coupon->save();
        }
        return response()->json(['is_success' => true, 'message' => __('Coupon status changed successfully.')]);
    }
}
