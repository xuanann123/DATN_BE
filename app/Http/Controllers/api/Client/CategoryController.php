<?php

namespace App\Http\Controllers\api\Client;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function getNameCategories()
    {
        $categories = Category::select('id', 'name', 'parent_id')->get();

        return response()->json([
            'status' => 200,
            'message' => 'Danh mục.',
            'data' => $categories
        ], 200);
    }
}
