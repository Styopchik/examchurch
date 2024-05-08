<?php

namespace App\Http\Controllers;

use App\DataTables\PageSettingDataTable;
use App\Http\Controllers\Controller;
use App\Models\PageSetting;
use Illuminate\Http\Request;

class PageSettingController extends Controller
{
    public function index(PageSettingDataTable $dataTable)
    {
        if (\Auth::user()->can('manage-page-setting')) {
            return $dataTable->render('page-settings.index');
        } else {
            return redirect()->back()->with('failed', __('Permission denied'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create-page-setting')) {
            return view('page-settings.create');
        } else {
            return redirect()->back()->with('failed', __('Permission denied'));
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('create-page-setting')) {
            request()->validate([
                'title'                     => 'required|string|max:191',
                'type'                      => 'required|string|max:191',
            ]);
            if ($request->type == 'link') {
                request()->validate([
                    'url_type'              => 'required|string|max:191',
                    'page_url'              => 'required|string|max:191',
                    'friendly_url'          => 'required|string|max:191',
                ]);
            } else {
                request()->validate([
                    'descriptions'          => 'required|string',
                ]);
            }
            $pageSetting                    = new PageSetting();
            $pageSetting->title             = $request->title;
            $pageSetting->type              = $request->type;
            if ($request->type == 'link') {
                $pageSetting->url_type      = $request->url_type;
                $pageSetting->page_url      = filter_var($request->page_url, FILTER_VALIDATE_URL) ? $request->page_url : url($request->page_url);
                $pageSetting->friendly_url  = filter_var($request->friendly_url, FILTER_VALIDATE_URL) ? $request->friendly_url : url($request->friendly_url);
            } else {
                $pageSetting->description   = $request->descriptions;
            }
            $pageSetting->save();
            return redirect()->route('page-setting.index')->with('success',  __('Page setting created successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied'));
        }
    }

    public function edit($id)
    {
        if (\Auth::user()->can('edit-page-setting')) {
            $pageSetting = PageSetting::find($id);
            return view('page-settings.edit', compact('pageSetting'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied'));
        }
    }

    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-page-setting')) {
            request()->validate([
                'title'                     => 'required|string|max:191',
                'type'                      => 'required|string|max:191',
            ]);
            if ($request->type == 'link') {
                request()->validate([
                    'url_type'              => 'required|string|max:191',
                    'page_url'              => 'required|string|max:191',
                    'friendly_url'          => 'required|string|max:191',
                ]);
            } else {
                request()->validate([
                    'descriptions'          => 'required|string',
                ]);
            }
            $pageSetting                    = PageSetting::find($id);
            $pageSetting->title             = $request->title;
            $pageSetting->type              = $request->type;
            if ($request->type == 'link') {
                $pageSetting->url_type      = $request->url_type;
                $pageSetting->page_url      = filter_var($request->page_url, FILTER_VALIDATE_URL) ? $request->page_url : url($request->page_url);
                $pageSetting->friendly_url  = filter_var($request->friendly_url, FILTER_VALIDATE_URL) ? $request->friendly_url : url($request->friendly_url);
            } else {
                $pageSetting->description   = $request->descriptions;
            }
            $pageSetting->save();
            return redirect()->route('page-setting.index')->with('success',  __('Page setting updated successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied'));
        }
    }

    public function destroy($id)
    {
        if (\Auth::user()->can('delete-page-setting')) {
            $pageSetting = PageSetting::find($id);
            $pageSetting->delete();
            return redirect()->back()->with('success', __('Page setting deleted successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied'));
        }
    }
}
