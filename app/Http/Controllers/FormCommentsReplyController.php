<?php

namespace App\Http\Controllers;

use App\Models\FormCommentsReply;
use Illuminate\Http\Request;

class FormCommentsReplyController extends Controller
{
    public function store(Request $request)
    {
        request()->validate([
            'name'          => 'required|string|max:191',
            'reply'         => 'required|string',
        ]);
        FormCommentsReply::create([
            'name'          => $request->name,
            'reply'         => $request->reply,
            'form_id'       => $request->form_id,
            'comment_id'    => $request->comment_id,
        ]);
        return redirect()->back();
    }
}
