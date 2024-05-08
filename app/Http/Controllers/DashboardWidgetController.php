<?php

namespace App\Http\Controllers;

use App\Facades\UtilityFacades;
use App\Models\DashboardWidget;
use App\Models\ImagePoll;
use App\Models\MeetingPoll;
use App\Models\MultipleChoice;
use App\Models\Poll;
use Illuminate\Http\Request;

class DashboardWidgetController extends Controller
{
    public function widgetChartData(Request $request)
    {
        $id                     = $request->widget;
        $dashboardWidget        = DashboardWidget::find($id);
        if ($dashboardWidget->type == "form") {
            $chartData          = UtilityFacades::widgetChartData($dashboardWidget->form_id);
            $label              = [];
            foreach ($chartData as $key => $value) {
                $name           = $value['name'];
                if ($name == $dashboardWidget->field_name) {
                    $label[$key]            = $value;
                    $label['id']            = $dashboardWidget->id;
                    $label['label']         = $value['label'];
                    $label['type']          = $dashboardWidget->type;
                    $label['chart_type']    = $dashboardWidget->chart_type;
                }
            }
            return response()->json($label, 200);
        } else {
            $chart              = Poll::find($dashboardWidget->poll_id);
            if ($chart->voting_type == "Multiple_choice") {
                $votes          = MultipleChoice::where('poll_id', $dashboardWidget->poll_id)->get();
                $chartData      = json_decode($chart->multiple_answer_options);
                $labels         = [];
                foreach ($chartData as $charts) {
                    foreach ($charts as $key => $value) {
                        $labels['options'][$value->answer_options]      = 0;
                        $labels['id']                                   = $dashboardWidget->id;
                        $labels['type']                                 = $dashboardWidget->type;
                        $labels['chart_type']                           = $dashboardWidget->chart_type;
                        $labels['label']                                = $chart->title;
                    }
                }
                foreach ($votes as $value) {
                    $labels['options'][$value->vote]++;
                }
            } elseif ($chart->voting_type == "Meeting_poll") {
                $votes                              = MeetingPoll::where('poll_id', $dashboardWidget->poll_id)->get();
                $chartData                          = json_decode($chart->meeting_answer_options);
                $labels                             = [];
                foreach ($chartData as $charts) {
                    foreach ($charts as $key => $value) {
                        $labels['options'][$value->datetime]    = 0;  //$value->datetime, UtilityFacades::dateTimeFormat($value->datetime)
                        $labels['id']                           = $dashboardWidget->id;
                        $labels['type']                         = $dashboardWidget->type;
                        $labels['chart_type']                   = $dashboardWidget->chart_type;
                        $labels['label']                        = $chart->title;
                    }
                }
                foreach ($votes as $value) {
                    $labels['options'][$value->vote]++;  //$value->vote  ,  UtilityFacades::dateTimeFormat($value->vote)
                }
            } else {
                $votes                              = ImagePoll::where('poll_id', $dashboardWidget->poll_id)->get();
                $chartData                          = json_decode($chart->image_answer_options);
                $labels                             = [];
                foreach ($chartData as $charts) {
                    foreach ($charts as $key => $value) {
                        $labels['options'][$value->optional_name]   = 0;
                        $labels['id']                               = $dashboardWidget->id;
                        $labels['type']                             = $dashboardWidget->type;
                        $labels['chart_type']                       = $dashboardWidget->chart_type;
                        $labels['label']                            = $chart->title;
                    }
                }
                foreach ($votes as $value) {
                    $labels['options'][$value->vote]++;
                }
            }
            $labels['created_by'] = $dashboardWidget->created_by;
            return response()->json($labels, 200);
        }
    }
}
