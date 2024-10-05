<?php

namespace App\Http\Controllers\Admin;

use App\Events\GreetingSent;
use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
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
        $message = Message::create([
            'message' => $request->message,
            'user_id' => $request->user()->id
        ]);

        broadcast(new MessageSent($request->user(), $request->message));

        return response()->json("Done bro");
    }
    // Lấy tất cả tin nhắn trong phòng chat chung
    public function fetchMessages()
    {
        $messages = Message::with('user')->get();
        return response()->json(['messages' => $messages]);
    }
    //Sự kiện chào user đối tượng trong phần chat receiver = đối phương mình muốn chào
    public function greetReceived(Request $request, User $receiver)
    {
        //Gửi thông tin màn hình đối phương
        broadcast(new GreetingSent($receiver, "{$request->user()->name} đã chào bạn!"));
        //Gửi thông tin màn hình của chính bạn
        broadcast(new GreetingSent($request->user(), "Bạn đã chào {$receiver->name}!"));
        
        return "Lời chào từ  {$request->user()->name} đến {$receiver->name}!";
    }
}
 