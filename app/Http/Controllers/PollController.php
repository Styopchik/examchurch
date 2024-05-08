<?php

namespace App\Http\Controllers;

use App\DataTables\PollDataTable;
use App\Facades\UtilityFacades;
use App\Models\Comments;
use App\Models\CommentsReply;
use App\Models\DashboardWidget;
use App\Models\ImagePoll;
use App\Models\MeetingPoll;
use App\Models\MultipleChoice;
use App\Models\Plan;
use App\Models\Poll;
use App\Models\User;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PollController extends Controller
{
    public function index(PollDataTable $dataTable)
    {
        if (\Auth::user()->can('manage-poll')) {
            return $dataTable->render('poll.index');
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create-poll')) {
            return view('poll.create');
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('create-poll')) {
            if ($request->voting_type == 'Multiple_choice') {
                request()->validate([
                    'multiple_answer_options.*.answer_options'          => 'required|string|max:191',
                    'title'                                             => 'nullable|string|max:191',
                    'description'                                       => 'nullable|string|max:191',
                    'voting_type'                                       => 'nullable|string|max:191',
                ]);
            } elseif ($request->voting_type == 'Image_poll') {
                request()->validate([
                    'image_answer_options.*.optional_name'              => 'required|string|max:191',
                    'image_answer_options.*.image'                      => 'required|image|mimes:png,jpg,jpeg',
                    'title'                                             => 'nullable|string|max:191',
                    'description'                                       => 'nullable|string|max:191',
                    'voting_type'                                       => 'nullable|string|max:191',
                ]);
            } else {
                request()->validate([
                    'meeting_answer_options.*.datetime'                 => 'required|date_format:Y-m-d H:i:s',
                    'title'                                             => 'nullable|string|max:191',
                    'description'                                       => 'nullable|string|max:191',
                    'voting_type'                                       => 'nullable|string|max:191',
                ]);
            }
            $user                                                       = User::find(\Auth::user()->admin_id);
            $polls                                                      = Poll::where('created_by', \Auth::user()->admin_id)->count();
            $plan                                                       = Plan::find($user->plan_id);
            if ($polls < $plan->max_polls) {
                if ($request->voting_type == 'Multiple_choice') {
                    $multipleAnswer['multiple_answer_options']          = $request->multiple_answer_options;
                    $pollAnswer['title']                                = $request->title;
                    $pollAnswer['description']                          = $request->description;
                    $pollAnswer['voting_type']                          = $request->voting_type;
                    $pollAnswer['multiple_answer_options']              = json_encode($multipleAnswer);
                    $pollAnswer['require_participants_names']           = ($request->require_participants_names == 'on') ? 1 : 0;
                    $pollAnswer['voting_restrictions']                  = $request->voting_restrictions;
                    $pollAnswer['set_end_date']                         = ($request->set_end_date == 'on') ? 1 : 0;
                    $pollAnswer['allow_comments']                       = ($request->allow_comments == 'on') ? 1 : 0;
                    $pollAnswer['hide_participants_from_each_other']    = ($request->hide_participants_from_each_other == 'on') ? 1 : 0;
                    $pollAnswer['results_visibility']                   = $request->results_visibility;
                    $pollAnswer['edit_vote_permissions']                = $request->edit_vote_permissions;
                    $pollAnswer['set_end_date_time']                    = Carbon::parse($request['set_end_date_time'])->toDateTimeString();
                    $pollAnswer['created_by']                           = Auth::user()->admin_id;
                    $pollAnswer                                         = Poll::create($pollAnswer);
                } else if ($request->voting_type == 'Image_poll') {
                    $images                 = $request->image_answer_options;
                    $imageAnswerOptions     = [];
                    foreach ($images as $img) {
                        $file               = $img['image'];
                        $filename           = $file->store('polls');
                        $imageAnswerOptions['image_answer_options'][] =  [
                            'optional_name' => $img['optional_name'],
                            'image'         => $filename
                        ];
                    }
                    $imagePollAnswer['title']                                       = $request->title;
                    $imagePollAnswer['description']                                 = $request->description;
                    $imagePollAnswer['voting_type']                                 = $request->voting_type;
                    $imagePollAnswer['image_answer_options']                        = json_encode($imageAnswerOptions);
                    $imagePollAnswer['image_require_participants_names']            = ($request->image_require_participants_names == 'on') ? 1 : 0;
                    $imagePollAnswer['image_voting_restrictions']                   = $request->image_voting_restrictions;
                    $imagePollAnswer['image_set_end_date']                          = ($request->image_set_end_date == 'on') ? 1 : 0;
                    $imagePollAnswer['image_set_end_date_time']                     = Carbon::parse($request['image_set_end_date_time'])->toDateTimeString();
                    $imagePollAnswer['image_allow_comments']                        = ($request->image_allow_comments == 'on') ? 1 : 0;
                    $imagePollAnswer['image_hide_participants_from_each_other']     = ($request->image_hide_participants_from_each_other == 'on') ? 1 : 0;
                    $imagePollAnswer['image_results_visibility']                    = $request->image_results_visibility;
                    $imagePollAnswer['image_edit_vote_permissions']                 = $request->image_edit_vote_permissions;
                    $imagePollAnswer['created_by']                                  = Auth::user()->admin_id;
                    $imagePollAnswer                                                = Poll::create($imagePollAnswer);
                } else {
                    $meetingMultipleAnswer['meeting_answer_options']                = $request->meeting_answer_options;
                    $meetingAnswerOptions                                           = [];
                    foreach ($meetingMultipleAnswer as $meetingMultiple) {
                        foreach ($meetingMultiple as $meeting) {
                            $meetingDateTime = Carbon::parse($meeting['datetime'])->toDateTimeString();
                            $meetingAnswerOptions['meeting_answer_options'][] =  [
                                'datetime' => $meetingDateTime
                            ];
                        }
                    }
                    $meetingPollAnswer['title']                                     = $request->title;
                    $meetingPollAnswer['description']                               = $request->description;
                    $meetingPollAnswer['voting_type']                               = $request->voting_type;
                    $meetingPollAnswer['meeting_answer_options']                    = json_encode($meetingAnswerOptions);
                    $meetingPollAnswer['meeting_fixed_time_zone']                   = ($request->meeting_fixed_time_zone == 'on') ? 1 : 0;
                    $meetingPollAnswer['meetings_fixed_time_zone']                  = $request->meetings_fixed_time_zone;
                    $meetingPollAnswer['limit_selection_to_one_option_only']        = ($request->limit_selection_to_one_option_only == 'on') ? 1 : 0;
                    $meetingPollAnswer['meeting_set_end_date']                      = ($request->meeting_set_end_date == 'on') ? 1 : 0;
                    $meetingPollAnswer['meeting_set_end_date_time']                 = Carbon::parse($request['meeting_set_end_date_time'])->toDateTimeString();
                    $meetingPollAnswer['meeting_allow_comments']                    = ($request->meeting_allow_comments == 'on') ? 1 : 0;
                    $meetingPollAnswer['meeting_hide_participants_from_each_other'] = ($request->meeting_hide_participants_from_each_other == 'on') ? 1 : 0;
                    $meetingPollAnswer['meeting_edit_vote_permissions']             = $request->meeting_edit_vote_permissions;
                    $meetingPollAnswer['created_by']                                = Auth::user()->admin_id;
                    $meetingPollAnswer                                              = Poll::create($meetingPollAnswer);
                }
                return redirect()->route('poll.index')->with('success', __('Poll created successfully.'));
            } else {
                return redirect()->route('poll.index')->with('failed', __('Your poll limit is over, please upgrade plan.'));
            }
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function poll(Request $request, $id)
    {
        if (\Auth::user()->can('vote-poll')) {
            $poll               = Poll::find($id);
            $commmant           = Comments::where('poll_id', $id)->get();
            $options            = json_decode($poll->multiple_answer_options);
            return view('poll.multiple-fill', compact('poll', 'options', 'commmant'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function fillStore(Request $request, $id)
    {
        $newSessId      = \Session::getId();
        $location       = \Location::get('103.74.73.193');
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $location   = \Location::get($request->ip());
        }
        $poll           = Poll::find($id);
        if ($poll->set_end_date == '1' && Carbon::now() >= $poll->set_end_date_time == true) {
            return redirect()->back()->with('failed', __('The date for voting has already expired.'));
        } else {
            if ($poll->voting_restrictions == 'One_vote_per_ip_address') {
                if (MultipleChoice::where('poll_id', $id)->where('location', $location->ip)->first()) {
                    return redirect()->back()->with('failed', __('You already voted on this poll.'));
                } else {
                    request()->validate([
                        'multiple_answer_options'   => 'required|string|max:191',
                    ]);
                    MultipleChoice::create([
                        'vote'                      => $request->multiple_answer_options,
                        'poll_id'                   => $id,
                        'location'                  => $location->ip,
                        'session_id'                => $newSessId,
                        'name'                      => $request->name,
                    ]);
                    return redirect()->back()->with('success', __('Voting successfully.'));
                }
            } else if ($poll->voting_restrictions == 'One_vote_per_browser_session') {
                if (MultipleChoice::where('poll_id', $id)->where('session_id', $newSessId)->first()) {
                    return redirect()->back()->with('failed', __('You already voted on this poll.'));
                } else {
                    request()->validate([
                        'multiple_answer_options'   => 'required|string|max:191',
                    ]);
                    MultipleChoice::create([
                        'vote'                      => $request->multiple_answer_options,
                        'poll_id'                   => $id,
                        'location'                  => $location->ip,
                        'session_id'                => $newSessId,
                        'name'                      => $request->name,
                    ]);
                    return redirect()->back()->with('success', __('Voting successfully.'));
                }
            } else {
                if (Auth::user()) {
                    request()->validate([
                        'multiple_answer_options'   => 'required|string|max:191',
                    ]);
                    MultipleChoice::create([
                        'vote'                      => $request->multiple_answer_options,
                        'poll_id'                   => $id,
                        'location'                  => $location->ip,
                        'session_id'                => $newSessId,
                        'name'                      => $request->name,
                    ]);
                    return redirect()->back()->with('success', __('Voting successfully.'));
                } else {
                    return redirect()->back()->with('failed', __('User account required. please sign up or log in to vote.'));
                }
            }
        }
    }

    public function pollResult(Request $request, $id)
    {
        if (\Auth::user()->can('result-poll')) {
            $poll       = Poll::find($id);
            $votes      = MultipleChoice::where('poll_id', $id)->get();
            $chartData  = json_decode($poll->multiple_answer_options);
            $options    = [];
            foreach ($chartData as $chart) {
                foreach ($chart as $key => $value) {
                    $options['options'][$value->answer_options] = 0;
                }
            }
            foreach ($votes as $value) {
                $options['options'][$value->vote]++;
            }
            return view('poll.multiple-result ', compact('votes', 'poll', 'options', 'chartData'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function pollImageResult(Request $request, $id)
    {
        if (\Auth::user()->can('result-poll')) {
            $poll       = Poll::find($id);
            $imgs       = json_decode($poll->image_answer_options);
            $votes      = ImagePoll::where('poll_id', $poll->id)->get();
            $chartData  = json_decode($poll->image_answer_options);
            $options    = [];
            foreach ($chartData as $chart) {
                foreach ($chart as $key => $value) {
                    $options['options'][$value->optional_name] = 0;
                }
            }
            foreach ($votes as $value) {
                $options['options'][$value->vote]++;
            }
            return view('poll.image-result', compact('poll', 'imgs', 'votes', 'options', 'chartData'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function pollMeetingResult(Request $request, $id)
    {
        if (\Auth::user()->can('result-poll')) {
            $poll       = Poll::find($id);
            $votes      = MeetingPoll::where('poll_id', $id)->get();
            $chartData  = json_decode($poll->meeting_answer_options);
            $options    = [];
            foreach ($chartData as $chart) {
                foreach ($chart as $key => $value) {
                    $options['options'][UtilityFacades::dateTimeFormat($value->datetime)] = 0;
                }
            }
            foreach ($votes as $value) {
                $options['options'][UtilityFacades::dateTimeFormat($value->vote)]++;
            }
            return view('poll.meeting-result ', compact('poll', 'options', 'chartData', 'votes'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function imagePoll(Request $request, $id)
    {
        if (\Auth::user()->can('vote-poll')) {
            $poll       = Poll::find($id);
            $options    = json_decode($poll->image_answer_options);
            return view('poll.image-fill', compact('poll', 'options'));
        } else {
            return redirect()->back()->with('failed', __('Permission Denied.'));
        }
    }

    public function imageStore(Request $request, $id)
    {
        $location       = \Location::get('103.74.73.193');
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $location   = \Location::get($request->ip());
        }
        $poll           = Poll::find($id);
        $newSessId      = \Session::getId();
        if ($poll->image_set_end_date == '1' && Carbon::now() >= $poll->image_set_end_date_time == true) {
            return redirect()->back()->with('failed', __('The date for voting has already expired.'));
        } else {
            if ($poll->image_voting_restrictions == 'One_vote_per_ip_address') {
                if (ImagePoll::where('poll_id', $id)->where('location', $location->ip)->first()) {
                    return redirect()->back()->with('failed', __('You already voted on this poll.'));
                } else {
                    request()->validate([
                        'optional_name'     => 'required|string|max:191',
                    ]);
                    ImagePoll::create([
                        'vote'              => $request->optional_name,
                        'poll_id'           => $id,
                        'location'          => $location->ip,
                        'name'              => $request->name,
                        'session_id'        => $newSessId,
                    ]);
                    return redirect()->back()->with('success', __('Voting Successfully.'));
                }
            } else if ($poll->image_voting_restrictions == 'One_vote_per_browser_session') {
                if (ImagePoll::where('poll_id', $id)->where('session_id', $newSessId)->first()) {
                    return redirect()->back()->with('failed', __('You already voted on this poll.'));
                } else {
                    request()->validate([
                        'optional_name'     => 'required|string|max:191',
                    ]);
                    ImagePoll::create([
                        'vote'              => $request->optional_name,
                        'poll_id'           => $id,
                        'location'          => $location->ip,
                        'name'              => $request->name,
                        'session_id'        => $newSessId,
                    ]);
                    return redirect()->back()->with('success', __('Voting Successfully.'));
                }
            } else {
                if (Auth::user()) {
                    request()->validate([
                        'optional_name' => 'required|string|max:191',
                    ]);
                    ImagePoll::create([
                        'vote'              => $request->optional_name,
                        'poll_id'           => $id,
                        'location'          => $location->ip,
                        'name'              => $request->name,
                        'session_id'        => $newSessId,
                    ]);
                    return redirect()->back()->with('success', __('Voting Successfully.'));
                } else {
                    return redirect()->back()->with('failed', __('User account required. please sign up or log in to vote.'));
                }
            }
        }
    }

    public function meetingPoll(Request $request, $id)
    {
        if (\Auth::user()->can('vote-poll')) {
            $poll = poll::find($id);
            $options = json_decode($poll->meeting_answer_options);
            return view('poll.meeting-fill', compact('poll', 'options'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function meetingStore(Request $request, $id)
    {
        $location       = \Location::get('103.74.73.193');
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $location   = \Location::get($request->ip());
        }
        $newSessId      = \Session::getId();
        $poll           = Poll::find($id);
        if ($poll->meeting_set_end_date == '1' && Carbon::now() >= $poll->meeting_set_end_date_time == true) {
            return redirect()->back()->with('failed', __('The date for voting has already expired.'));
        } else {
            request()->validate([
                'meeting_answer_options'    => 'required|array',
                'name'                      => 'required|string|max:191',
            ]);
            foreach ($request->meeting_answer_options as $meeting_answer) {
                MeetingPoll::create([
                    'vote'                  => $meeting_answer,
                    'poll_id'               => $id,
                    'location'              => $location->ip,
                    'name'                  => $request->name,
                    'session_id'            => $newSessId,

                ]);
            }
            return redirect()->back()->with('success', __('Voting successfully.'));
        }
    }

    public function publicFill(Request $request, $id)
    {
        $hashids        = new Hashids('', 20);
        $id             = $hashids->decodeHex($id);
        if ($id) {
            $poll       = Poll::find($id);
            $formValue  = null;
            if ($poll) {
                $array  = $poll->getPollArray();
                return view('poll.public-multiple-choice', compact('poll', 'formValue', 'array'));
            } else {
                return redirect()->back()->with('failed', __('Form not found.'));
            }
        } else {
            abort(404);
        }
    }

    public function publicFillResult(Request $request, $id)
    {
        $hashids        = new Hashids('', 20);
        $id             = $hashids->decodeHex($id);
        $poll           = Poll::find($id);
        $newSessId      = \Session::getId();
        $location       = \Location::get('103.74.73.193');
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $location   = \Location::get($request->ip());
        }
        if ($poll->results_visibility == 'public_after_vote') {
            if ($poll->voting_restrictions == 'One_vote_per_ip_address') {
                if ($ip_address = MultipleChoice::where('poll_id', $id)->where('location', $location->ip)->first()) {
                    if ($id) {
                        $formValue      = null;
                        $votes          = MultipleChoice::where('poll_id', $id)->get();
                        $chartData      = json_decode($poll->multiple_answer_options);
                        if ($poll) {
                            $options    = [];
                            foreach ($chartData as $chart) {
                                foreach ($chart as $key => $value) {
                                    $options['options'][$value->answer_options] = 0;
                                }
                            }
                            foreach ($votes as $value) {
                                $options['options'][$value->vote]++;
                            }
                            return view('poll.public-multiple-choice-result', compact('poll', 'formValue', 'options', 'chartData', 'votes'));
                        } else {
                            return redirect()->back()->with('failed', __('Form not found.'));
                        }
                    } else {
                        abort(404);
                    }
                } else {
                    return redirect()->back()->with('failed', __('After vote results visibility.'));
                }
            } else if ($poll->voting_restrictions == 'One_vote_per_browser_session') {
                if ($ip_address = MultipleChoice::where('poll_id', $id)->where('session_id', $newSessId)->first()) {
                    if ($id) {
                        $formValue      = null;
                        $votes          = MultipleChoice::where('poll_id', $id)->get();
                        $chartData      = json_decode($poll->multiple_answer_options);
                        if ($poll) {
                            $options    = [];
                            foreach ($chartData as $chart) {
                                foreach ($chart as $key => $value) {
                                    $options['options'][$value->answer_options] = 0;
                                }
                            }
                            foreach ($votes as $value) {
                                $options['options'][$value->vote]++;
                            }
                            return view('poll.public-multiple-choice-result', compact('poll', 'formValue', 'options', 'chartData', 'votes'));
                        } else {
                            return redirect()->back()->with('failed', __('Form not found.'));
                        }
                    } else {
                        abort(404);
                    }
                } else {
                    return redirect()->back()->with('failed', __('After vote results visibility.'));
                }
            } else {
                return redirect()->back()->with('failed', __('Only vote results visibility in user.'));
            }
        } else {
            if ($id) {
                $formValue      = null;
                $votes          = MultipleChoice::where('poll_id', $id)->get();
                $chartData      = json_decode($poll->multiple_answer_options);
                if ($poll) {
                    $options    = [];
                    foreach ($chartData as $chart) {
                        foreach ($chart as $key => $value) {
                            $options['options'][$value->answer_options] = 0;
                        }
                    }
                    foreach ($votes as $value) {
                        $options['options'][$value->vote]++;
                    }
                    return view('poll.public-multiple-choice-result', compact('poll', 'formValue', 'options', 'chartData', 'votes'));
                } else {
                    return redirect()->back()->with('failed', __('Form not found.'));
                }
            } else {
                abort(404);
            }
        }
    }

    public function publicFillMeeting(Request $request, $id)
    {
        $hashids        = new Hashids('', 20);
        $id             = $hashids->decodeHex($id);
        if ($id) {
            $poll       = Poll::find($id);
            $formValue  = null;
            if ($poll) {
                $options = $poll->getMeetingArray();
                return view('poll.public-meeting-poll', compact('poll', 'formValue', 'options'));
            } else {
                return redirect()->back()->with('failed', __('Form not found.'));
            }
        } else {
            abort(404);
        }
    }

    public function publicFillResultMeeting(Request $request, $id)
    {
        $hashids            = new Hashids('', 20);
        $id                 = $hashids->decodeHex($id);
        if ($id) {
            $poll           = Poll::find($id);
            $votes          = MeetingPoll::where('poll_id', $id)->get();
            $chartData      = json_decode($poll->meeting_answer_options);
            $options        = [];
            $formValue      = null;
            if ($poll) {
                foreach ($chartData as $chart) {
                    foreach ($chart as $key => $value) {
                        $options['options'][UtilityFacades::dateTimeFormat($value->datetime)] = 0;
                    }
                }
                foreach ($votes as $value) {
                    $options['options'][UtilityFacades::dateTimeFormat($value->vote)]++;
                }
                return view('poll.public-meeting-result', compact('poll', 'formValue', 'options', 'chartData', 'votes'));
            } else {
                return redirect()->back()->with('failed', __('Form not found.'));
            }
        } else {
            abort(404);
        }
    }

    public function publicFillImage(Request $request, $id)
    {
        $hashids            = new Hashids('', 20);
        $id                 = $hashids->decodeHex($id);
        if ($id) {
            $poll           = Poll::find($id);
            $formValue      = null;
            if ($poll) {
                $options    = $poll->getPollImage();
                return view('poll.public-image-poll', compact('poll', 'formValue', 'options'));
            } else {
                return redirect()->back()->with('failed', __('Form not found.'));
            }
        } else {
            abort(404);
        }
    }

    public function publicFillResultImage(Request $request, $id)
    {
        $hashids        = new Hashids('', 20);
        $id             = $hashids->decodeHex($id);
        $newSessId      = \Session::getId();
        $location       = \Location::get('103.74.73.193');
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $location   = \Location::get($request->ip());
        }
        $poll           = Poll::find($id);
        if ($poll->image_results_visibility == 'public_after_vote') {
            if ($poll->image_voting_restrictions == 'One_vote_per_ip_address') {
                if ($ip_address = ImagePoll::where('poll_id', $id)->where('location', $location->ip)->first()) {
                    if ($id) {
                        $imgs       = json_decode($poll->image_answer_options);
                        $votes      = ImagePoll::where('poll_id', $poll->id)->get();
                        $chartData  = json_decode($poll->image_answer_options);
                        $options    = [];
                        $formValue  = null;
                        if ($poll) {
                            foreach ($chartData as $chart) {
                                foreach ($chart as $key => $value) {
                                    $options['options'][$value->optional_name] = 0;
                                }
                            }
                            foreach ($votes as $value) {
                                $options['options'][$value->vote]++;
                            }
                            return view('poll.public-image-result', compact('poll', 'formValue', 'imgs', 'options', 'votes', 'chartData'));
                        } else {
                            return redirect()->back()->with('failed', __('Form not found.'));
                        }
                    } else {
                        abort(404);
                    }
                } else {
                    return redirect()->back()->with('failed', __('After Vote Results Visibility.'));
                }
            } else if ($poll->image_voting_restrictions == 'One_vote_per_browser_session') {
                if ($ip_address = ImagePoll::where('poll_id', $id)->where('session_id', $newSessId)->first()) {
                    if ($id) {
                        $imgs       = json_decode($poll->image_answer_options);
                        $votes      = ImagePoll::where('poll_id', $poll->id)->get();
                        $chartData  = json_decode($poll->image_answer_options);
                        $options    = [];
                        $formValue  = null;
                        if ($poll) {
                            foreach ($chartData as $chart) {
                                foreach ($chart as $key => $value) {
                                    $options['options'][$value->optional_name] = 0;
                                }
                            }
                            foreach ($votes as $value) {
                                $options['options'][$value->vote]++;
                            }
                            return view('poll.public-image-result', compact('poll', 'formValue', 'imgs', 'options', 'votes', 'chartData'));
                        } else {
                            return redirect()->back()->with('failed', __('Form not found.'));
                        }
                    } else {
                        abort(404);
                    }
                } else {
                    return redirect()->back()->with('failed', __('After vote results visibility.'));
                }
            } else {
                return redirect()->back()->with('failed', __('Only Vote Results Visibility In User.'));
            }
        } else {
            if ($id) {
                $imgs       = json_decode($poll->image_answer_options);
                $votes      = ImagePoll::where('poll_id', $poll->id)->get();
                $chartData  = json_decode($poll->image_answer_options);
                $options    = [];
                $formValue  = null;
                if ($poll) {
                    foreach ($chartData as $chart) {
                        foreach ($chart as $key => $value) {
                            $options['options'][$value->optional_name] = 0;
                        }
                    }
                    foreach ($votes as $value) {
                        $options['options'][$value->vote]++;
                    }
                    return view('poll.public-image-result', compact('poll', 'formValue', 'imgs', 'options', 'votes', 'chartData'));
                } else {
                    return redirect()->back()->with('failed', __('Form not found.'));
                }
            } else {
                abort(404);
            }
        }
    }

    public function share($id)
    {
        $hashids        = new Hashids('', 20);
        $id             = $hashids->decodeHex($id);
        $poll           = Poll::find($id);
        $view           = view('poll.public-multiple-share', compact('poll'));
        return ['html' => $view->render()];
    }

    public function shareQr($id)
    {
        $hashids        = new Hashids('', 20);
        $id             = $hashids->decodeHex($id);
        $poll           = Poll::find($id);
        $view           =   view('poll.public-multiple-share-new', compact('poll'));
        return ['html' => $view->render()];
    }

    public function shareQrImage($id)
    {
        $hashids        = new Hashids('', 20);
        $id             = $hashids->decodeHex($id);
        $poll           = Poll::find($id);
        $view           = view('poll.public-image-share-new', compact('poll'));
        return ['html' => $view->render()];
    }

    public function shareImage($id)
    {
        $hashids        = new Hashids('', 20);
        $id             = $hashids->decodeHex($id);
        $poll           = Poll::find($id);
        $view           = view('poll.public-image-share', compact('poll'));
        return ['html' => $view->render()];
    }

    public function shareMeeting($id)
    {
        $hashids        = new Hashids('', 20);
        $id             = $hashids->decodeHex($id);
        $poll           = Poll::find($id);
        $view           = view('poll.public-meeting-share', compact('poll'));
        return ['html' => $view->render()];
    }

    public function shareQrMeeting($id)
    {
        $hashids        = new Hashids('', 20);
        $id             = $hashids->decodeHex($id);
        $poll           = Poll::find($id);
        $view           = view('poll.public-meeting-share-new', compact('poll'));
        return ['html' => $view->render()];
    }

    public function shares($id)
    {
        $poll           = Poll::find($id);
        $view           = view('poll.multiple-share', compact('poll'));
        return ['html' => $view->render()];
    }

    public function shareMeetings($id)
    {
        $poll           = Poll::find($id);
        $view           = view('poll.meeting-share', compact('poll'));
        return ['html' => $view->render()];
    }

    public function shareImages($id)
    {
        $poll           = Poll::find($id);
        $view           = view('poll.image-share', compact('poll'));
        return ['html' => $view->render()];
    }

    public function destroy($id)
    {
        if (\Auth::user()->can('delete-poll')) {
            $poll                       = Poll::find($id);
            $multipleChoice             = MultipleChoice::where('poll_id', $id)->get();
            $meetingPoll                = MeetingPoll::where('poll_id', $id)->get();
            $imagePoll                  = ImagePoll::where('poll_id', $id)->get();
            $comments                   = Comments::where('poll_id', $id)->get();
            $commentsReply              = CommentsReply::where('poll_id', $id)->get();
            DashboardWidget::where('poll_id', $id)->delete();
            if ($poll->voting_type == 'Multiple_choice') {
                foreach ($multipleChoice as $value) {
                    $ids                = $value->id;
                    $multiple           = MultipleChoice::find($ids);
                    if ($multiple) {
                        $multiple->delete();
                    }
                }
            } elseif ($poll->voting_type == 'Meeting_poll') {
                foreach ($meetingPoll as $meetingValue) {
                    $meetingValueIds    = $meetingValue->id;
                    $meeting            = MeetingPoll::find($meetingValueIds);
                    if ($meeting) {
                        $meeting->delete();
                    }
                }
            } else {
                $imgs = json_decode($poll->image_answer_options);
                foreach ($imgs->image_answer_options as $img) {
                    $imageName          = $img->image;
                    if ($imageName) {
                        Storage::delete($imageName);
                    }
                }
                foreach ($imagePoll as $imagePollValue) {
                    $imagePollValueIds  = $imagePollValue->id;
                    $image              = ImagePoll::find($imagePollValueIds);
                    if ($image) {
                        $image->delete();
                    }
                }
            }
            foreach ($comments as $allcomments) {
                $commentsids            = $allcomments->id;
                $commentsall            = Comments::find($commentsids);
                if ($commentsall) {
                    $commentsall->delete();
                }
            }
            foreach ($commentsReply as $commentsReplyAll) {
                $commentsReplyIds       = $commentsReplyAll->id;
                $reply                  =  CommentsReply::find($commentsReplyIds);
                if ($reply) {
                    $reply->delete();
                }
            }
            $poll->delete();
            return redirect()->back()->with('success', __('Poll deleted successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function edit($id)
    {
        if (\Auth::user()->can('edit-poll')) {
            $poll                       = Poll::find($id);
            $next                       = Poll::where('id', '>', $poll->id)->first();
            $previous                   = Poll::where('id', '<', $poll->id)->orderBy('id', 'desc')->first();
            $imageAnswerOptions         = json_decode($poll->image_answer_options);
            $meetingAnswerOptions       = json_decode($poll->meeting_answer_options);
            $multipleAnswerOptions      = json_decode($poll->multiple_answer_options);
            $multiple                   = [];
            $meetingPoll                = [];
            $imagePoll                  = [];
            if ($poll->voting_type == 'Multiple_choice') {
                foreach ($multipleAnswerOptions as $value) {
                    foreach ($value as $data) {
                        $multiple[] = [
                            'answer_options'    => $data->answer_options
                        ];
                    }
                }
            } else if ($poll->voting_type == 'Meeting_poll') {
                foreach ($meetingAnswerOptions as $value) {
                    foreach ($value as $data) {
                        $meetingPoll[] = [
                            'datetime'          => $data->datetime
                        ];
                    }
                }
            } else {
                foreach ($imageAnswerOptions as $value) {
                    foreach ($value as $data) {
                        $imagePoll[] = [
                            'optional_name'     => $data->optional_name,
                            'image'             => Storage::url($data->image),
                            'old_image'         => $data->image
                        ];
                    }
                }
            }
            return view('poll.edit', compact('poll', 'multiple', 'meetingPoll', 'imagePoll', 'next', 'previous'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit-poll')) {
            request()->validate([
                'title'                                             => 'required|string|max:191',
            ]);
            if ($request->voting_type == 'Image_poll') {
                request()->validate([
                    'image_answer_options.*.optional_name'          => 'required|string|max:191',
                    'image_answer_options.*.old_image'              => 'image|mimes:png,jpg,jpeg',
                    'image_answer_options.*.new_image'              => 'image|mimes:png,jpg,jpeg',
                ]);
            }
            if ($request->voting_type == 'Multiple choice') {
                $pollAnswer = Poll::find($id);
                $multipleAnswer['multiple_answer_options']          = $request->multiple_answer_options;
                $pollAnswer['title']                                = $request->title;
                $pollAnswer['description']                          = $request->description;
                $pollAnswer['voting_type']                          = 'Multiple_choice';
                $pollAnswer['multiple_answer_options']              = json_encode($multipleAnswer);
                $pollAnswer['require_participants_names']           = ($request->require_participants_names == 'on') ? 1 : 0;
                $pollAnswer['voting_restrictions']                  = $request->voting_restrictions;
                $pollAnswer['set_end_date']                         = ($request->set_end_date == 'on') ? 1 : 0;
                $pollAnswer['allow_comments']                       = ($request->allow_comments == 'on') ? 1 : 0;
                $pollAnswer['hide_participants_from_each_other']    = ($request->hide_participants_from_each_other == 'on') ? 1 : 0;
                $pollAnswer['results_visibility']                   = $request->results_visibility;
                $pollAnswer['set_end_date_time']                    = Carbon::parse($request['set_end_date_time'])->toDateTimeString();
                $pollAnswer->save();
            } else if ($request->voting_type == 'Image poll') {
                $imagePollAnswer            = Poll::find($id);
                $images                     = $request->image_answer_options;
                $imageAnswerOptions         = [];
                foreach ($images as $img) {
                    if ($img['old_image']) {
                        $imageAnswerOptions['image_answer_options'][] =  [
                            'optional_name' => $img['optional_name'],
                            'image'         => $img['old_image']
                        ];
                    } else {
                        $file               = $img['new_image'];
                        $filename           = $file->store('polls');
                        $imageAnswerOptions['image_answer_options'][] =  [
                            'optional_name' => $img['optional_name'],
                            'image'         => $filename
                        ];
                    }
                }
                $imagePollAnswer['title']                                       = $request->title;
                $imagePollAnswer['description']                                 = $request->description;
                $imagePollAnswer['voting_type']                                 = 'Image_poll';
                $imagePollAnswer['image_answer_options']                        = json_encode($imageAnswerOptions);
                $imagePollAnswer['image_require_participants_names']            = ($request->image_require_participants_names == 'on') ? 1 : 0;
                $imagePollAnswer['image_voting_restrictions']                   = $request->image_voting_restrictions;
                $imagePollAnswer['image_set_end_date']                          = ($request->image_set_end_date == 'on') ? 1 : 0;
                $imagePollAnswer['image_set_end_date_time']                     = Carbon::parse($request['image_set_end_date_time'])->toDateTimeString();
                $imagePollAnswer['image_allow_comments']                        = ($request->image_allow_comments == 'on') ? 1 : 0;
                $imagePollAnswer['image_hide_participants_from_each_other']     = ($request->image_hide_participants_from_each_other == 'on') ? 1 : 0;
                $imagePollAnswer['image_results_visibility']                    = $request->image_results_visibility;
                $imagePollAnswer->save();
            } else {
                $meetingPollAnswer                                              = Poll::find($id);
                $meetingMultipleAnswer['meeting_answer_options']                = $request->meeting_answer_options;
                $meetingAnswerOptions                                           = [];
                foreach ($meetingMultipleAnswer as $meetingMultiple) {
                    foreach ($meetingMultiple as $meeting) {
                        $meetingDateTime                                        = Carbon::parse($meeting['datetime'])->toDateTimeString();
                        $meetingAnswerOptions['meeting_answer_options'][]       = [
                            'datetime' => $meetingDateTime
                        ];
                    }
                }
                $meetingPollAnswer['title']                                         = $request->title;
                $meetingPollAnswer['description']                                   = $request->description;
                $meetingPollAnswer['voting_type']                                   = 'Meeting_poll';
                $meetingPollAnswer['meeting_answer_options']                        = json_encode($meetingAnswerOptions);
                $meetingPollAnswer['meeting_fixed_time_zone']                       = ($request->meeting_fixed_time_zone == 'on') ? 1 : 0;
                $meetingPollAnswer['meetings_fixed_time_zone']                      = $request->meetings_fixed_time_zone;
                $meetingPollAnswer['limit_selection_to_one_option_only']            = ($request->limit_selection_to_one_option_only == 'on') ? 1 : 0;
                $meetingPollAnswer['meeting_set_end_date']                          = ($request->meeting_set_end_date == 'on') ? 1 : 0;
                $meetingPollAnswer['meeting_set_end_date_time']                     = Carbon::parse($request['meeting_set_end_date_time'])->toDateTimeString();
                $meetingPollAnswer['meeting_allow_comments']                        = ($request->meeting_allow_comments == 'on') ? 1 : 0;
                $meetingPollAnswer['meeting_hide_participants_from_each_other']     = ($request->meeting_hide_participants_from_each_other == 'on') ? 1 : 0;
                $meetingPollAnswer->save();
            }
            return redirect()->route('poll.index')->with('success', __('Poll created successfully.'));
        } else {
            return redirect()->back()->with('failed', __('Permission denied.'));
        }
    }
}
