<?php

namespace App\Http\Controllers;

use App\DataTables\PlanDataTable;
use App\Facades\UtilityFacades;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanController extends Controller
{
    public function index(PlanDataTable $dataTable)
    {
        if (\Auth::user()->can('manage-plan')) {
            if (Auth::user()->type == 'Super Admin') {
                return $dataTable->render('plans.index');
            } else {
                $plans = Plan::where('active_status', 1)->get();
                $user  = User::where('id', Auth::user()->id)->first();
                return view('plans.index', compact('user', 'plans'));
            }
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create-plan')) {
            return view('plans.create');
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('create-plan')) {
            request()->validate([
                'name'          => 'required|max:191|unique:plans,name',
                'price'         => 'required|numeric',
                'duration'      => 'required|numeric',
                'durationtype'  => 'required|string|max:191',
                'max_users'     => 'required|integer',
                'max_roles'     => 'required|integer',
                'max_form'      => 'required|integer',
                'max_booking'   => 'required|integer',
                'max_documents' => 'required|integer',
                'max_polls'     => 'required|integer',
                'description'   => 'string|max:50',
            ]);
            Plan::create([
                'name'          => $request->name,
                'price'         => $request->price,
                'duration'      => $request->duration,
                'durationtype'  => $request->durationtype,
                'max_users'     => $request->max_users,
                'max_roles'     => $request->max_roles,
                'max_form'      => $request->max_form,
                'max_booking'   => $request->max_booking,
                'max_documents' => $request->max_documents,
                'max_polls'     => $request->max_polls,
                'description'   => $request->description,
            ]);
            return redirect()->route('plans.index')
                ->with('success', __('Plan created successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function edit($id)
    {
        if (\Auth::user()->can('edit-plan')) {
            $plan = Plan::find($id);
            return view('plans.edit', compact('plan'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-plan')) {
            request()->validate([
                'name'              => 'required|max:191|unique:plans,name,' . $id,
                'price'             => 'required|numeric',
                'duration'          => 'required|numeric',
                'durationtype'      => 'required|string|max:191',
                'max_users'         => 'required|integer',
                'max_roles'         => 'required|integer',
                'max_form'          => 'required|integer',
                'max_booking'       => 'required|integer',
                'max_documents'     => 'required|integer',
                'max_polls'         => 'required|integer',
                'description'       => 'string|max:50',
            ]);
            $plan                   = Plan::find($id);
            $plan->name             = $request->input('name');
            $plan->price            = $request->input('price');
            $plan->duration         = $request->input('duration');
            $plan->durationtype     = $request->input('durationtype');
            $plan->max_users        = $request->input('max_users');
            $plan->max_roles        = $request->input('max_roles');
            $plan->max_form         = $request->input('max_form');
            $plan->max_booking      = $request->input('max_booking');
            $plan->max_documents    = $request->input('max_documents');
            $plan->max_polls        = $request->input('max_polls');
            $plan->description      = $request->input('description');
            $plan->save();
            return redirect()->route('plans.index')
                ->with('success', __('Plan updated successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (\Auth::user()->can('delete-plan')) {
            $plan = Plan::find($id);
            if ($plan->id != 1) {
                $planExistInOrder = Order::where('plan_id', $plan->id)->first();
                if (empty($planExistInOrder)) {
                    $plan->delete();
                    return redirect()->route('plans.index')->with('success', __('Plan deleted successfully.'));
                } else {
                    return redirect()->back()->with('failed', __('Can not delete this plan because its purchased by users.'));
                }
            } else {
                return redirect()->back()->with('failed', __('Can not delete this plan because its free plan.'));
            }
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function planStatus($id, Request $request)
    {
        $plan = Plan::find($id);
        $input = ($request->value == "true") ? 1 : 0;
        if ($plan) {
            $plan->active_status = $input;
            $plan->save();
        }
        return response()->json(['is_success' => true, 'message' => __('Plan status changed successfully.')]);
    }

    public function payment($code)
    {
        $planId                 = \Illuminate\Support\Facades\Crypt::decrypt($code);
        $plan                   = Plan::find($planId);
        $paymentTypes           = UtilityFacades::getpaymenttypes();
        $adminPaymentSetting    = UtilityFacades::getAdminPaymentSettings();
        if ($plan) {
            return view('plans.payment', compact('plan', 'adminPaymentSetting', 'paymentTypes'));
        } else {
            return redirect()->back()->with('errors', __('Plan deleted successfully.'));
        }
    }
}
