<?php

namespace App\Http\Controllers;

use App\DataTables\FormTemplateDataTable;
use App\Models\FormTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FormTemplateController extends Controller
{
    public function index(FormTemplateDataTable $dataTable)
    {
        if (\Auth::user()->can('manage-form-template')) {
            return $dataTable->render('form-template.index');
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create-form-template')) {
            return view('form-template.create');
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('create-form-template')) {
            request()->validate([
                'title' => 'required|string|max:191',
                'image' => 'required|image|mimes:jpeg,jpg,png',
            ]);
            $fileName       = '';
            if ($request->file('image')) {
                $file       = $request->file('image');
                $fileName   =  $file->store('form-template');
            }
            FormTemplate::create([
                'title'         => $request->title,
                'image'         => $fileName,
                'created_by'    => Auth::user()->admin_id,
            ]);
            return redirect()->route('form-template.index')->with('success', __('Form Template created succesfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }
    public function edit($id)
    {
        if (\Auth::user()->can('edit-form-template')) {
            $formTemplate = FormTemplate::find($id);
            return view('form-template.edit', compact('formTemplate'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-form-template')) {
            request()->validate([
                'title' => 'required|string|max:191',
                'image' => 'required|image|mimes:jpeg,jpg,png',
            ]);
            $formTemplate               = FormTemplate::find($id);
            if ($request->hasfile('image')) {
                $file                   = $request->file('image');
                $fileName               = $file->store('form-template');
                $formTemplate->image    = $fileName;
            }
            $formTemplate->title        = $request->title;
            $formTemplate->created_by   = Auth::user()->admin_id;
            $formTemplate->save();
            return redirect()->route('form-template.index')->with('success', __('Form Template updated successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (\Auth::user()->can('delete-form-template')) {
            $formTemplate = FormTemplate::find($id);
            if (File::exists(Storage::path($formTemplate->image))) {
                Storage::delete($formTemplate->image);
            }
            $formTemplate->delete();
            return redirect()->back()->with('success', __('Form Template Deleted succesfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function status(Request $request, $id)
    {
        $formTemplate   = FormTemplate::find($id);
        $input          = ($request->value == "true") ? 1 : 0;
        if ($formTemplate) {
            $formTemplate->status = $input;
            $formTemplate->save();
        }
        return response()->json(['is_success' => true, 'message' => __('Form Template status changed successfully.')]);
    }

    public function design($id)
    {
        if (\Auth::user()->can('design-form-template')) {
            $formTemplate = FormTemplate::find($id);
            return view('form-template.design', compact('formTemplate'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function designUpdate(Request $request, $id)
    {
        if (\Auth::user()->can('design-form-template')) {
            request()->validate([
                'json'              => 'required|json',
            ]);
            $formtemplate               = FormTemplate::find($id);
            $designJsons                = json_decode($request->json, true);
            foreach ($designJsons as &$jsons) {
                foreach ($jsons as  &$json) {
                    if ($json['type'] == 'repeater') {
                        $name                                   = $json['name'] . '-preview';
                        $repeaterJsons                          = $request->{$name};
                        foreach ($repeaterJsons as $repeaterJsonKey => $repeaterJson) {
                            $validationRules["$name.$repeaterJsonKey.label"] = 'required|max:191';
                            $validationRules["$name.$repeaterJsonKey.value"] = 'required|max:191';
                            $validationRules["$name.$repeaterJsonKey.image"] = 'image|mimes:jpeg,png,jpg|max:2048';
                        }
                        $validator = \Validator::make($request->all(), $validationRules, [
                            "$name.*.label.required"            => 'The ' . $json['label'] . ' label field is required.',
                            "$name.*.label.max"                 => 'The ' . $json['label'] . ' label field may not be greater than :max characters.',
                            "$name.*.value.required"            => 'The ' . $json['label'] . ' value field is required.',
                            "$name.*.value.max"                 => 'The ' . $json['label'] . ' value field may not be greater than :max characters.',
                            "$name.*.image.image"               => 'The ' . $json['label'] . ' must be an image.',
                            "$name.*.image.mimes"               => 'The ' . $json['label'] . ' must be a valid image format.',
                            "$name.*.image.max"                 => 'The ' . $json['label'] . ' may not be greater than :max kilobytes.',
                        ]);
                        if ($validator->fails()) {
                            $messages                           = $validator->errors();
                            return redirect()->back()->with('errors', $messages->first());
                        }
                        foreach ($repeaterJsons as &$repeaterJson) {
                            if (isset($repeaterJson['image'])) {
                                $file                           = $repeaterJson['image'];
                                $repeaterJson['image']          = $file->store('form-template/' . $formtemplate->id . '/image-choices');
                            } else {
                                $repeaterValueJsons             = json_decode($json['value'], true);
                                if (is_array($repeaterValueJsons)) {
                                    foreach ($repeaterValueJsons as $repeaterValueJson) {
                                        if ($repeaterJson['value'] === $repeaterValueJson['value']) {
                                            $repeaterJson['image'] = $repeaterValueJson['image'];
                                        }
                                    }
                                }
                            }
                        }
                        $json['value']                          = json_encode($repeaterJsons);
                    }
                }
            }
            $formtemplate->json         = json_encode($designJsons);
            $formtemplate->created_by   = Auth::user()->admin_id;
            $formtemplate->save();
            return redirect()->route('form-template.index')->with('success', __('Form Template design updated successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }
}
