<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function index()
    {
        $title = 'Quản lý đánh giá';
        return view('admin.ratings.list', compact('title'));
    }
}
