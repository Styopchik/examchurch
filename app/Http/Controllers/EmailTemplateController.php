<?php

namespace App\Http\Controllers;

use App\DataTables\EmailTemplateDataTable;
use Illuminate\Http\Request;
use Spatie\MailTemplates\Models\MailTemplate;

class EmailTemplateController extends Controller
{
    public function index(EmailTemplateDataTable $dataTable)
    {
        if (\Auth::user()->can('manage-email-template')) {
            return $dataTable->render('email-template.index');
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function edit($id)
    {
        if (\Auth::user()->can('edit-email-template')) {
            $mailTemplate = MailTemplate::find($id);
            return view('email-template.edit', compact('mailTemplate'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-email-template')) {
            request()->validate([
                'subject'       => 'required|string|unique:mail_templates,subject,' . $id,
                'html_template' => 'required|string',
            ]);
            $input              = $request->all();
            $mailTemplate       = MailTemplate::find($id);
            $mailTemplate->update($input);
            return redirect()->route('email-template.index')->with('success', __('Email template updated successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        $mailTemplate           = MailTemplate::find($id);
        $mailTemplate->delete();
        return redirect()->back()->with('success', __('Email template deleted successfully.'));
    }
}
