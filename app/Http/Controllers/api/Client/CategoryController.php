<?php

namespace App\Http\Controllers\api\Client;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function getNameCategories()
    {
        try {
            $categories = Category::select('id', 'name', 'parent_id')->get();

            if ($categories->isEmpty()) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không có danh mục nào.',
                    'data' => []
                ], 404);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Danh mục.',
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi trong quá trình lấy danh mục.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
