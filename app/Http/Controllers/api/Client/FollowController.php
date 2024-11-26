<?php

namespace App\Http\Controllers\api\Client;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\Notification;
use App\Models\User;
use App\Notifications\FollowNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function checkFollow(Request $request)
    {
        $userId = $request->id_user;
        $teacherId = $request->id_teacher;

        $checkFollow = Follow::where([
            ['follower_id', '=', $userId],
            ['following_id', '=', $teacherId]
        ])->first();

        if (!$checkFollow) {
            return response()->json([
                'message' => 'Chưa theo dõi',
                'data' => [
                    'action' => 'follow'
                ]
            ]);
        }

        return response()->json([
            'message' => 'Đã theo dõi',
            'data' => [
                'action' => 'unfollow'
            ]
        ]);
    }

    public function follow(Request $request)
    {
        try {
            $usersAdmin = User::where('user_type', 'admin')->get();
            //id người sẽ được theo dõi
            $following_id = $request->following_id;
            //Lấy id người dùng đang online hiện tại
            $follower = Auth::user();
            $following = User::find($following_id);
            if (!$following) {
                return response()->json([
                    "status" => "error",
                    'message' => "Không tồn tại người mà bạn muốn theo dõi",
                    "data" => [],
                ], 404);
            }
            //Kiểm tra xem người dùng này đã follow chưa
            if (!$follower->following()->where("following_id", $following->id)->exists()) {
                $follower->following()->attach($following->id);
                //Lấy thằng vừa được thêm dữ liệu vào bảng trung gian
                $follow = Follow::where([
                    ['follower_id', '=', $follower->id],
                    ['following_id', '=', $following->id]
                ])->firstOrFail();
                //Lưu thông báo
                $following = $following->notify(new FollowNotification($follow));

                return response()->json([
                    "status" => "success",
                    "message" => "Theo dõi thành công",
                    "data" => [
                        "follow" => $follow
                    ],
                ], 201);
            }
            //Đã theo dõi từ trước sẽ báo lỗi 400
            return response()->json([
                "status" => "error",
                "message" => "Bạn đã theo dõi user này",
                "data" => [],
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => $e->getMessage(),
                "data" => [],
            ], 500);
        }

    }
    public function unfollow(Request $request)
    {
        $follower = Auth::user();
        $following_id = $request->following_id;
        //Kiểm tra xem tài khoản trên đã được follow chưa và tồn tại không
        $following = User::find($following_id);
        if (!$following) {
            return response()->json([
                "status" => "error",
                'message' => "Không tồn tại người mà có theo dõi",
                "data" => [],
            ], 404);
        }
        //Kiểm tra xem nó có follow không
        if ($follower->following()->where("following_id", $following_id)->exists()) {
            $follower->following()->detach($following_id);
            return response()->json([
                "status" => "success",
                "message" => "Huỷ theo dõi tài khoản thành công",
                "data" => [],
            ], 200);
        }
        return response()->json([
            "status" => "error",
            "message" => "Tài khoản này chưa được theo dõi",
            "data" => [],
        ], 400);
    }
}
