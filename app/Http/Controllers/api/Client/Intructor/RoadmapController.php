<?php

namespace App\Http\Controllers\api\Client\Intructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Roadmap\StorePhaseRequest;
use App\Http\Requests\Client\Roadmap\StoreRoadmapRequest;
use App\Http\Requests\Client\Roadmap\UpdateRoadmapRequest;
use App\Models\Phase;
use App\Models\Roadmap;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
                    $query->with(['courses:id,name,thumbnail,level,price,price_sale,description,slug']);
                }
            ])
                ->where('user_id', $user->id)
                ->get();

            return response()->json([
                'success' => true,
                'message' => "Danh sách lộ trình",
                'data' => $roadmaps
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
    public function roadmapDetail(Roadmap $roadmap)
    {
        try {
            $detailRoadmap = Roadmap::with([
                'phases' => function ($query) {
                    // Sắp xếp phases theo order
                    $query->orderBy('order');
                    // Lấy các khóa học liên kết trong phases (chỉ lấy id, name, thumbnail, level)
                    $query->with(['courses:id,name,thumbnail,level,price,price_sale,description,slug']);
                }
            ])
                ->with(['user:id,name,avatar'])
                ->where('id', $roadmap->id)
                ->first();
            return response()->json([
                'success' => true,
                'message' => "Chi tiết lộ trình",
                'data' => $detailRoadmap
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => true,
                'message' => "Đã xảy ra lỗi " . $e->getMessage(),
                'data' => []
            ], 500);
        }

    }


    //Thêm lộ trình
    public function storeRoadmap(StoreRoadmapRequest $request)
    {
        //title với description
        try {
            //Lấy người dùng hiện tại đang thao tác
            $user = auth()->user();
            $valid = $request->validated();
            //Đi lưu ảnh vào localstorage
            if ($request->hasFile('thumbnail')) {
                $valid['thumbnail'] = $request->file('thumbnail')->store('roadmap');
            }
            //Lưu dữ liệu database
            $roadmap = Roadmap::create([
                'user_id' => $user->id,
                'name' => $valid['name'],
                'description' => $valid['description'],
                'sort_description' => $valid['sort_description'],
                'thumbnail' => $valid['thumbnail'],
            ]);
            return response()->json([
                'success' => 'success',
                'message' => 'Tạo lộ trình thành công',
                'data' => $roadmap
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
    //Sửa lộ trình
    public function updateRoadmap(UpdateRoadmapRequest $request, $id)
    {
        try {
            $roadmap = Roadmap::findOrFail($id);
            $valid = $request->validated();
            $thumbnail = $roadmap->thumbnail;

            // Nếu có ảnh mới, xử lý lưu file
            if ($request->hasFile('thumbnail')) {
                $newThumbnail = $request->file('thumbnail')->store('roadmap');
                if ($newThumbnail) {
                    $valid['thumbnail'] = $newThumbnail;
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không thể lưu ảnh mới',
                    ], 500);
                }
            }

            // Cập nhật dữ liệu lộ trình
            $roadmap->update([
                'name' => $valid['name'],
                'description' => $valid['description'],
                'sort_description' => $valid['sort_description'],
                'thumbnail' => $valid['thumbnail'] ?? $thumbnail,
            ]);

            // Nếu lưu thành công và có ảnh mới, xóa ảnh cũ
            if ($request->hasFile('thumbnail') && Storage::exists($thumbnail)) {
                Storage::delete($thumbnail);
            }

            return response()->json([
                'success' => true,
                'message' => 'Sửa lộ trình thành công',
                'data' => $roadmap
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    //Xoá lộ trình 
    public function destroyRoadmap(Roadmap $roadmap)
    {
        try {
            if (!$roadmap) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy lộ trình',
                ], 204);
            }
            if ($roadmap->thumbnail && Storage::exists($roadmap->thumbnail)) {
                Storage::delete($roadmap->thumbnail);
            }
            $roadmap->delete();
            return response()->json([
                'success' => true,
                'message' => 'Xoá lộ trình thành công',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
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
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }

    }
    public function updatePhase(Request $request, Phase $phase)
    {
        try {


            // Cập nhật thông tin giai đoạn
            $phase->update([
                'name' => $request['name'],
                'description' => $request['description'],
                'order' => $request['order'],
            ]);

            // Cập nhật danh sách khóa học liên kết
            if (isset($request['course_ids'])) {
                $phase->courses()->sync($request['course_ids']);
            }

            return response()->json([
                'success' => 'success',
                'message' => 'Cập nhật giai đoạn thành công',
                'data' => $phase,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
    public function destroyPhase(Phase $phase)
    {
        try {
            // Xóa các khóa học liên kết qua bảng trung gian
            $phase->courses()->detach();

            // Xóa giai đoạn
            $phase->delete();

            return response()->json([
                'success' => 'success',
                'message' => 'Xóa giai đoạn thành công',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


}
