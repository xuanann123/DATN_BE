<?php

namespace App\Http\Controllers\api\Client;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function getBanners()
    {
        $banners = Banner::select('id', 'title', 'redirect_url', 'image', 'position', 'start_time', 'end_time')
            ->where('is_active', '=', 1)
            ->orderByDesc('position')
            ->get();


        if ($banners->isEmpty()) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Empty banner list',
            ], 204);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Get banners successfully!',
            'data' => $banners
        ], 200);
    }
}
