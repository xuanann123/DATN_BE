<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public static function checkFollow($studentId, $teacherId)
    {
        $checkFollow = Follow::where('follower_id', $studentId)->where('following_id', $teacherId)->first();
        if ($checkFollow) {
            return true;
        }
        return false;
    }

    public function  follow(Request $request) {
        $studentId = $request->id_student;
        $teacherId = $request->id_teacher;

        $newFollow = Follow::query()->create([
            'follower_id' => $studentId,
            'following_id' => $teacherId
        ]);

        if ($newFollow) {
            return back()->with(['success' => 'Theo dỗi thành công']);
        }

        return back()->with(['error' => 'Theo dõi thất bại']);
    }

    public function  unFollow(Request $request) {
        $studentId = $request->id_student;
        $teacherId = $request->id_teacher;

        $checkFollow = Follow::where('follower_id', $studentId)->where('following_id', $teacherId)->first();
        if ($checkFollow) {
            $checkFollow->delete();
            return back()->with(['success' => 'Hủy theo dõi thành công']);
        }

        return back()->with(['error' => 'Hủy theo dõi thất bại']);
    }
}
