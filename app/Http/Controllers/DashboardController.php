<?php

namespace App\Http\Controllers;

use App\DataTables\DashboardWidgetDataTable;
use App\Models\DashboardWidget;
use App\Models\Form;
use App\Models\Poll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(DashboardWidgetDataTable $dataTable)
    {
        if (\Auth::user()->can('manage-dashboard-widget')) {
            return $dataTable->render('dashboard.index');
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (\Auth::user()->can('create-dashboard-widget')) {
            $form               = Form::all();
            $poll               = Poll::all();
            $type               = [];
            $type['']           = __('Please select type');
            if (count($form) > 0) {
                $type['form']   = "Form";
            }
            if (count($poll) > 0) {
                $type['poll']   = "Poll";
            }
            $forms              = [];
            $forms['']          = __('No select title');
            foreach ($form as $val) {
                if ($val->created_by == Auth::user()->admin_id) {
                    $forms[$val->id]    = $val->title;
                }
            }
            $polls = [];
            $polls[''] = __('No select title');
            foreach ($poll as $value) {
                if ($value->created_by == Auth::user()->admin_id) {
                    $polls[$value->id]  = $value->title;
                }
            }
            return view('dashboard.create', compact('forms', 'polls', 'type'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (\Auth::user()->can('create-dashboard-widget')) {
            request()->validate([
                'title'      => 'required|string|max:191',
                'size'       => 'required|numeric',
                'type'       => 'required|string|max:191',
                'chart_type' => 'required|string|max:191',
                'form_title' => 'nullable|integer',
                'field_name' => 'nullable|string|max:191',
                'poll_title' => 'nullable|integer',
            ]);
            if ($request->type == 'form') {
                $wid                    = DashboardWidget::orderBy('id', 'DESC')->first();
                $widget                 = new DashboardWidget();
                $widget->title          = $request->title;
                $widget->size           = $request->size;
                $widget->type           = $request->type;
                $widget->form_id        = $request->form_title;
                $widget->field_name     = $request->field_name;
                $widget->chart_type     = $request->chart_type;
                $widget->created_by     = Auth::user()->id;
                $widget->position       = (!empty($wid) ? ($wid->position + 1) : 0);
                $widget->save();
            } else {
                $wid                    = DashboardWidget::orderBy('id', 'DESC')->first();
                $widget                 = new DashboardWidget();
                $widget->title          = $request->title;
                $widget->size           = $request->size;
                $widget->type           = $request->type;
                $widget->poll_id        = $request->poll_title;
                $widget->chart_type     = $request->chart_type;
                $widget->created_by     = Auth::user()->id;
                $widget->position       = (!empty($wid) ? ($wid->position + 1) : 0);
                $widget->save();
            }
            return redirect()->route('dashboard.index')
                ->with('success', __('Dashboard created successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (\Auth::user()->can('edit-dashboard-widget')) {
            $dashboard                  = DashboardWidget::find($id);
            $form                       = Form::all();
            $polls                      = [];
            $forms                      = [];
            $label                      = [];
            $poll                       = Poll::all();
            if ($dashboard->type == 'form') {
                foreach ($form as $val) {
                    $forms[$val->id]    = $val->title;
                }
                $formTitle              =  Form::find($dashboard->form_id);
                $formtitles             = json_decode($formTitle->json);
                foreach ($formtitles as $formtitle) {
                    foreach ($formtitle as $formtitlekey => $formtitleval) {
                        if ($formtitleval->type == 'select' || $formtitleval->type == 'radio-group' || $formtitleval->type == 'date' || $formtitleval->type == 'checkbox-group' || $formtitleval->type == 'starRating') {
                            $label[$formtitleval->name] = $formtitleval->label;
                        }
                    }
                }
            } else {
                foreach ($poll as $val) {
                    $polls[$val->id]    = $val->title;
                }
            }
            return view('dashboard.edit', compact('dashboard', 'polls', 'label', 'forms'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-dashboard-widget')) {
            request()->validate([
                'title'      => 'required|string|max:191',
                'size'       => 'required|numeric',
                'type'       => 'required|string|max:191',
                'chart_type' => 'required|string|max:191',
                'form_title' => 'nullable|integer',
                'field_name' => 'nullable|string|max:191',
                'poll_title' => 'nullable|integer',
            ]);
            $dashboard                  = DashboardWidget::find($id);
            $dashboard->title           = $request->title;
            $dashboard->size            = $request->size;
            $dashboard->type            = $request->type;
            if ($request->type == 'form') {
                $dashboard->form_id     = $request->form_title;
                $dashboard->field_name  = $request->field_name;
            } else {
                $dashboard->poll_id     = $request->poll_title;
            }
            $dashboard->chart_type      = $request->chart_type;
            $dashboard->update();
            return redirect()->route('dashboard.index')->with('success', __('Dashboard updated successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (\Auth::user()->can('delete-dashboard-widget')) {
            $dashboard = DashboardWidget::find($id);
            $dashboard->delete();
            return redirect()->route('dashboard.index')
                ->with('success', __('Dashboard deleted successfully'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function updateDashboard(Request $request)
    {
        if (\Auth::user()->can('manage-dashboard-widget')) {
            $widgets            = $request->all();
            foreach ($widgets['position'] as $key => $item) {
                $dash           = DashboardWidget::find($item);
                $dash->position = $key;
                $dash->save();
            }
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function widgetChnages(Request $request)
    {
        $label              = [];
        $widget             = $request->widget;
        $form               = Form::find($widget);
        $home               = json_decode($form->json);
        foreach ($home as $hom) {
            foreach ($hom as $key => $var) {
                if ($var->type == 'select' || $var->type == 'radio-group' || $var->type == 'date' || $var->type == 'checkbox-group' || $var->type == 'starRating') {
                    $label[$key] = $var;
                }
            }
        }
        return response()->json($label, 200);
    }
}
