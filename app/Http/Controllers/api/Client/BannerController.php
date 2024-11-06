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
                //nằm trong khoảng thời gian start_time - end_time
                ->orderByDesc('position')
                ->get();
            //Kiểm tra nếu dữ liệu rỗng
            if ($banners->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Danh sách banner rỗng',
                ], status: 404);
            }
            //Nếu thành thông trả về dữ liệu 200
            return response()->json([
                'status' => 'success',
                'message' => 'Lấy danh sách banner thành công!',
                'data' => $banners
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
