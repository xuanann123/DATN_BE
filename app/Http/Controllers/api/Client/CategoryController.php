<?php

namespace App\Http\Controllers\api\Client;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function getNameCategories()
    {
        //Sử dụng try catch khi lấy trả về dữ liệu
        try {
            //Lấy những trường dữ liệu trong database.
            $categories = Category::select('id', 'slug', 'name')
            ->get();
            //Kiểm tra dữ nếu rỗng thì trả về 204
            if ($categories->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không có danh mục nào.',
                    'data' => []
                ], status: 404);
            }
            //Lấy dữ liệu thành công thì trả về 200
            return response()->json([
                'status' => 'success',
                'message' => 'Danh mục.',
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            //Nếu lỗi dữ liệu server thì trả về 500
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi trong quá trình lấy danh mục.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getCatHasPosts()
    {
        //Sử dụng try catch khi lấy trả về dữ liệu
        try {
            //Lấy những trường dữ liệu trong database.
            $categories = Category::select('id', 'slug', 'name')
                ->whereHas('posts')
                ->get();
            //Kiểm tra dữ nếu rỗng thì trả về 204
            if ($categories->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không có danh mục nào.',
                    'data' => []
                ], status: 404);
            }
            //Lấy dữ liệu thành công thì trả về 200
            return response()->json([
                'status' => 'success',
                'message' => 'Danh mục.',
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            //Nếu lỗi dữ liệu server thì trả về 500
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi trong quá trình lấy danh mục.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
