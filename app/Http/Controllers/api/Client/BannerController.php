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
            ->where('is_active', '=', 0)
            ->orderByDesc('position')
            ->get();


        if ($banners->isEmpty()) {
            return response()->json([
                'status' => 204,
                'message' => 'Empty banner list',
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Get banner successfully',
            'data' => $banners
        ]);
    }
}
