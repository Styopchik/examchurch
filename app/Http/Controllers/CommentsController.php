<?php

namespace App\Http\Controllers;

use App\Models\Comments;
use App\Models\CommentsReply;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
    public function store(Request $request)
    {
        request()->validate([
            'name'      => 'required|string|max:191',
            'comment'   => 'required|string',
        ]);
        Comments::create([
            'name'      => $request->name,
            'comment'   => $request->comment,
            'poll_id'   => $request->poll_id,
        ]);
        return redirect()->back();
    }

    public function destroy($id)
    {
        $comments       = Comments::find($id);
        $commentsReply  = CommentsReply::where('comment_id', $id)->get();
        foreach ($commentsReply as $value) {
            $ids        = $value->id;
            $reply      = CommentsReply::find($ids);
            $reply->delete();
        }
        $comments->delete();
        return redirect()->back()->with('success', __('Comment deleted successfully.'));
    }
}
