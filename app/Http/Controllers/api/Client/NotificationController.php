<?php

namespace App\Http\Controllers\api\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Lấy danh sách thông báo
    public function index()
    {
        try {
            $user = auth()->user();
            $counts = request()->get('counts', 10);

            $notifications = $user->notifications()
                ->where('notifiable_id', $user->id)
                ->orderBy('created_at', 'DESC')
                ->take($counts)
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Danh sách thông báo',
                'data' => $notifications,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi lấy danh sách thông báo.',
                'error' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    // Lấy số lượng thông báo chưa đọc
    public function getUnreadCount()
    {
        try {
            $user = auth()->user();

            $allNotifications = $user->notifications()
                ->where('notifiable_id', $user->id)
                ->count();

            $unreadCount = $user->notifications()->whereNull('read_at')->count();

            return response()->json([
                'status' => 'success',
                'message' => 'Số lượng thông báo chưa đọc.',
                'data' => [
                    'unreadCount' => $unreadCount,
                    'allNotifications' => $allNotifications
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi lấy số lượng thông báo chưa đọc.',
                'error' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    // Đánh dấu thông báo là đã đọc
    public function markAsRead($id)
    {
        try {
            $notification = Auth::user()->notifications()->findOrFail($id);

            // Cập nhật trạng thái đã đọc cho thông báo
            $notification->update([
                'read_at' => now()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Đã đọc thông báo.',
                'data' => []
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi đánh dấu thông báo là đã đọc.',
                'error' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    // Xóa thông báo
    public function delete($id)
    {
        try {
            $notification = Auth::user()->notifications()->findOrFail($id);

            $notification->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Đã xóa thông báo.',
                'data' => []
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi xóa thông báo.',
                'error' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
    public function getNotificationPost() {
        try {

            $listNotification = Post::with('user:id,name,avatar', 'tags:id,name,slug', 'categories:id,name,slug')->where('is_notification', 1)->select('id', 'title', 'description', 'content', 'thumbnail', 'slug', 'created_at', 'views', 'user_id')->limit(10)->latest('created_at')->get();
            if ($listNotification->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Không có thông báo từ admin',
                    'data' => [],
                ], 204);
            }
                return response()->json([
                    'status' => 'success',
                    'message' => 'Danh sách notification',
                    'data' => $listNotification,
                ], 200);



        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Loi khi lay danh sach notification.',
                'error' => $e->getMessage(),
                'data' => []
            ], 500);
        }   
    }

}
