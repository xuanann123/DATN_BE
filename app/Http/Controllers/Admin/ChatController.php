<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        $title = 'Tin nhắn';
        return view('admin.chat.index', compact('title'));
    }
}
