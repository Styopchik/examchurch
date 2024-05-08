<?php

namespace App\Http\Controllers;

use App\Models\CommentsReply;
use Illuminate\Http\Request;

class CommentsReplyController extends Controller
{
    public function store(Request $request)
    {
        request()->validate([
            'name'          => 'required|string|max:191',
            'reply'         => 'required|string',
        ]);
        CommentsReply::create([
            'name'          => $request->name,
            'reply'         => $request->reply,
            'poll_id'       => $request->poll_id,
            'comment_id'    => $request->comment_id,
        ]);
        return redirect()->back();
    }
}
