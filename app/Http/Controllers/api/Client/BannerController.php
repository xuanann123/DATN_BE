<?php

namespace App\Http\Controllers\api\Client;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function getBanners()
    {
        
        try {
            //Lấy danh sách banner
            $banners = Banner::select('id', 'title', 'redirect_url', 'image', 'content', 'position', 'start_time', 'end_time')
                ->where('is_active', '=', 1)
                ->orderByDesc('position')
                ->get();
            //Kiểm tra nếu dữ liệu rỗng
            if ($banners->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Empty banner list',
                ], status: 404);
            }
            //Chỉnh sửa đường dẫn url
            $bannersWithUrls = $banners->map(function ($banner) {
                return [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'redirect_url' => $banner->redirect_url,
                    'image' => url(Storage::url($banner->image)),
                    'content' => $banner->content,
                    'position' => $banner->position,
                    'start_time' => $banner->start_time,
                    'end_time' => $banner->end_time,
                ];
            });
            //Nếu thành thông trả về dữ liệu 200
            return response()->json([
                'status' => 'success',
                'message' => 'Get banners successfully!',
                'data' => $bannersWithUrls
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
        
    }
}
