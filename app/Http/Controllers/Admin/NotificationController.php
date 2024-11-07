<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notification;

class NotificationController extends Controller
{
    // protected $user;

    // public function __construct() {
    //     $this->user = auth()->user();
    // }

    public function index()
    {
        $user = auth()->user();

        $counts = request()->get('counts', 10);

        $notifications = $user->notifications()
            ->where('notifiable_id', $user->id)
            ->orderBy('created_at', 'DESC')
            ->take($counts)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $notifications,
            'message' => ''
        ]);
    }

    public function getUnreadCount()
    {
        $user = auth()->user();

        $allNotifications = $user->notifications()
            ->where('notifiable_id', $user->id)
            ->count();
        $unreadCount = $user->notifications()->whereNull('read_at')->count();
        return response()->json([
            'unreadCount' => $unreadCount,
            'allNotifications' => $allNotifications
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->update([
            'read_at' => now()
        ]);
        return response()->json(['success' => true]);
    }
}
