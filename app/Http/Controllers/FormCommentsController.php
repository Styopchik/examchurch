<?php

namespace App\Http\Controllers;

use App\Models\FormComments;
use App\Models\FormCommentsReply;
use Illuminate\Http\Request;

class FormCommentsController extends Controller
{
    public function store(Request $request)
    {
        request()->validate([
            'name'      => 'required|string|max:191',
            'comment'   => 'required|string',
        ]);
        FormComments::create([
            'name'      => $request->name,
            'comment'   => $request->comment,
            'form_id'   => $request->form_id,
        ]);
        return redirect()->back();
    }

    public function destroy($id)
    {
        $comments       = FormComments::find($id);
        $commentsReply  = FormCommentsReply::where('comment_id', $id)->get();
        foreach ($commentsReply as $value) {
            $ids        = $value->id;
            $reply      = FormCommentsReply::find($ids);
            $reply->delete();
        }
        $comments->delete();
        return redirect()->back()->with('successful', __('Form comment deleted successfully.'));
    }
}
