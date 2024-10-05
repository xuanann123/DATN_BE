<?php

namespace App\Http\Controllers\Admin;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        // dd(request()->user());
        $title = 'Tin nhắn';
        return view('admin.chat.index', compact('title'));
    }
    public function messageReceived(Request $request) {
        $request->validate([
            'message' => 'required',
        ]);

        broadcast(new MessageSent($request->user(), $request->message));

        return response()->json("Done bro");
    }
}
 