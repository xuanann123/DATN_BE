<?php

namespace App\Http\Controllers\api\Client\Intructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Roadmap\StorePhaseRequest;
use App\Http\Requests\Client\Roadmap\StoreRoadmapRequest;
use App\Models\Phase;
use App\Models\Roadmap;
use Illuminate\Http\Request;

class RoadmapController extends Controller
{
    public function getRoadmap()
    {
        // Lấy người dùng hiện tại
        try {
            $user = auth()->user();

            // Truy vấn các lộ trình của người dùng, sắp xếp phases theo order
            $roadmaps = Roadmap::with([
                'phases' => function ($query) {
                    // Sắp xếp phases theo order
                    $query->orderBy('order');
                    // Lấy các khóa học liên kết trong phases (chỉ lấy id, name, thumbnail, level)
                    $query->with(['courses:id,name,thumbnail,level']);
                }
            ])
                ->where('user_id', $user->id)
                ->get();

            return response()->json([
                'success' => true,
                'message' => "Danh sách lộ trình",
                'data' => $roadmaps
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
    }

    public function storeRoadmap(StoreRoadmapRequest $request)
    {
        //title với description
        try {
            //Lấy người dùng hiện tại đang thao tác
            $user = auth()->user();
            $valid = $request->validated();
            $roadmap = Roadmap::create([
                'user_id' => $user->id,
                'name' => $valid['name'],
                'description' => $valid['description'],
            ]);
            return response()->json([
                'success' => 'success',
                'message' => 'Tạo lộ trình thành công',
                'data' => $roadmap
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
    }
    public function storePhase(StorePhaseRequest $request)
    {
        try {
            $validated = $request->validated();
            // Tạo giai đoạn mới
            $phase = Phase::create([
                'id_roadmap' => $validated['roadmap_id'], // ID lộ trình
                'name' => $validated['name'],       // Tên giai đoạn
                'description' => $validated['description'], // Mô tả giai đoạn
                'order' => $validated['order'],      // Thứ tự giai đoạn
            ]);
            // Liên kết các khóa học với giai đoạn qua bảng trung gian (course_phase)
            $phase->courses()->attach($validated['course_ids']);
            return response()->json([
                'success' => 'success',
                'message' => 'Tạo giai đo thành công',
                'data' => $phase
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }

    }
}
