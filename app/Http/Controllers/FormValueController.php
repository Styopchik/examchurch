<?php

namespace App\Http\Controllers;

use App\DataTables\FormValuesDataTable;
use App\Exports\FormValuesExport;
use App\Facades\UtilityFacades;
use App\Models\Form;
use App\Models\FormValue;
use App\Models\UserForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class FormValueController extends Controller
{
    public function showSubmitedForms($formID, FormValuesDataTable $dataTable)
    {
        if (\Auth::user()->can('manage-submitted-form')) {
            $form       = Form::find($formID);
            $chartData  = UtilityFacades::chartData($formID);
            return $dataTable->with('form_id', $formID)->render('form-value.index', compact('chartData', 'form'));
        } else {
            return redirect()->back()->with('errors', __('Permission denied.'));
        }
    }

    public function show($id)
    {
        if (\Auth::user()->can('show-submitted-form')) {
            try {
                $formValue  = FormValue::find($id);
                $array      = json_decode($formValue->json);
            } catch (\Throwable $th) {
                return redirect()->back()->with('errors', $th->getMessage());
            }
            return view('form-value.view', compact('formValue', 'array'));
        } else {
            return redirect()->back()->with('errors', __('Permission denied.'));
        }
    }

    public function edit($id)
    {
        $user               = \Auth::user();
        $userRole           = $user->roles->first()->id;
        $formValue          = FormValue::find($id);
        $formAllowedEdit    = UserForm::where('role_id', $userRole)->where('form_id', $formValue->form_id)->count();
        if (\Auth::user()->can('edit-submitted-form') && $user->type == 'Admin') {
            $array          = json_decode($formValue->json);
            $form           = Form::find($formValue->form_id);
            return view('form.fill', compact('form', 'formValue', 'array'));
        } else {
            if (\Auth::user()->can('edit-submitted-form') && $formAllowedEdit > 0) {
                $formValue  = FormValue::find($id);
                $array      = json_decode($formValue->json);
                $form       = Form::find($formValue->form_id);
                return view('form.fill', compact('form', 'formValue', 'array'));
            } else {
                return redirect()->back()->with('errors', __('Permission denied.'));
            }
        }
    }

    public function destroy($id)
    {
        if (\Auth::user()->can('delete-submitted-form')) {
            FormValue::find($id)->delete();
            return redirect()->back()->with('success',  __('Form deleted successfully.'));
        } else {
            return redirect()->back()->with('errors', __('Permission denied.'));
        }
    }

    public function downloadPdf($id)
    {
        $formValue          = FormValue::find($id);
        if ($formValue) {
            $formValue->createPDF();
        } else {
            $formValue      = FormValue::find($id);
            if (!$formValue) {
                $id         = Crypt::decryptString($id);
                $formValue  = FormValue::find($id);
            }
            if ($formValue) {
                $formValue->createPDF();
            } else {
                return redirect()->route('home')->with('errors', __('File is not exist.'));
            }
        }
    }

    public function exportXlsx(Request $request)
    {
        $form                           = Form::find($request->form_id);
        if ($request->select_date != '') {
            $dateRange                  = $request->select_date;
            list($startDate, $endDate)  = array_map('trim', explode('to', $dateRange));
        } else {
            $startDate                  = '';
            $endDate                    = '';
        }
        return Excel::download(new FormValuesExport($request, $startDate, $endDate), $form->title . '.xlsx');
    }

    public function videoStore(Request $request)
    {
        $file           = $request->file('media');
        $fileName       = $file->store('form_video');
        $values         = $fileName;
        return response()->json(['success' => __('Video uploded successfully.'), 'filename' => $values]);
    }

    public function selfieDownload($id)
    {
        $formValue      = FormValue::find($id);
        $json           = $formValue->json;
        $jsonData       = json_decode($json, true);
        $selfieValue    = null;
        foreach ($jsonData[0] as $field) {
            if ($field['type'] === 'selfie') {
                $selfieValue = $field['value'];
                break;
            } elseif ($field['type'] === 'video') {
                $selfieValue = $field['value'];
                break;
            }
        }
        if ($selfieValue === null) {
            return redirect()->back()->with('errors', __('Image Value Not Found'));
        }
        $filePath       = storage_path('app/' . $selfieValue);
        return response()->download($filePath);
    }

    public function PaymentSlipDownload($id)
    {
        $formValue           = FormValue::find($id);
        if ($formValue->payment_type == 'offlinepayment') {
            $filePath = storage_path('app/' . $formValue->transfer_slip);

            if (isset($filePath) && Storage::exists($formValue->transfer_slip)) {
                return response()->download($filePath);
            }else{
                return redirect()->back()->with('errors', __('Payment Slip Not Found'));
            }
        } else {
            return redirect()->back()->with('errors', __('invalid payment method'));
        }
    }

    // change form fill edit Lock Status
    public function formFillEditlock($id)
    {
        $form = FormValue::find($id);
        if ($form->form_edit_lock_status == 0) {
            $form->form_edit_lock_status = 1;
            $form->save();
        } else {
            $form->form_edit_lock_status = 0;
            $form->save();
        }
        return redirect()->back()->with('success', __('Form edit Lock status changed successfully.'));
    }
}
