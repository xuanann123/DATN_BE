<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{

    public function index()
    {
        $banners = Banner::select('id', 'title', 'redirect_url', 'image', 'position', 'start_time', 'end_time', 'is_active')
            ->orderByDesc('position')
            ->get();
        return view('admin.banners.index', compact('banners'));
    }


    public function create()
    {
        return view('admin.banners.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'redirect_url' => 'nullable|url',
            'image' => 'required|image|max:5120',
            'position' => 'nullable|integer|min:0',
            'start_time' => 'nullable|date_format:Y-m-d\TH:i',
            'end_time' => 'nullable|date_format:Y-m-d\TH:i',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề',
            'title.max' => 'Tiêu đề dưới 255 kí tự',
            'redirect_url' => 'Url không hợp lệ',
            'image.required' => 'Vui lòng tải ảnh lên',
            'image.image' => 'Đây không phải ảnh',
            'image.max' => 'Kích thước ảnh không quá 5mb',
            'position.integer' => 'Position phải là số nguyên',
            'position.min' => 'Position phải là số dương',
            'start_time.date_format' => 'Thời gian phải gồm ngày, giờ, tháng, năm',
            'end_time.date_format' => 'Thời gian phải gồm ngày, giờ, tháng, năm',
        ]);

        $data = $request->except('image');

        if ($request->image && $request->hasFile('image')) {
            $pathImage = Storage::putFile('banners', $request->file('image'));
            $fullNameImage = env('URL') . 'storage/' . $pathImage;

            $data['image'] = $fullNameImage;
        }

        $newBanner = Banner::query()->create($data);

        if (!$newBanner) {
            return back()->with(['error' => 'Create banner failed!']);
        }

        return redirect()->route('.adminbanners.index')->with(['message' => 'Create banner successfully!']);
    }

    public function edit(string $id)
    {
        $banner = Banner::find($id);

        if (!$banner) {
            return back()->with(['error' => 'Banner not exit!']);
        }

        return view('admin.banners.edit', compact('banner'));
    }


    public function update(Request $request, string $id)
    {

        $request->validate([
            'title' => 'required|max:255',
            'redirect_url' => 'nullable|url',
            'position' => 'nullable|integer|min:0',
            'start_time' => 'nullable|date_format:Y-m-d\TH:i',
            'end_time' => 'nullable|date_format:Y-m-d\TH:i',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề',
            'title.max' => 'Tiêu đề dưới 255 kí tự',
            'redirect_url.url' => 'Url không hợp lệ',
            'position.integer' => 'Position phải là số nguyên',
            'position.min' => 'Position phải là số dương',
            'start_time.date_format' => 'Thời gian phải gồm ngày, giờ, tháng, năm',
            'end_time.date_format' => 'Thời gian phải gồm ngày, giờ, tháng, năm',
        ]);

        $data = $request->except('image');

        if (!$request->is_active) {
            $data['is_active'] = 0;
        }

        $banner = Banner::find($id);

        if (!$banner) {
            return redirect()->route('.adminbanners.index')->with(['error' => 'Banner not exit!']);
        }

        if ($request->image && $request->hasFile('image')) {
            $pathImage = Storage::putFile('banners', $request->file('image'));

            $fullNameImage = env('URL') . 'storage/' . $pathImage;

            $data['image'] = $fullNameImage;

            $subStringImage = substr($banner->image, strlen(env('URL')));

            if ($subStringImage && file_exists($subStringImage)) {
                unlink($subStringImage);
            }
        } else {
            $data['image'] = $banner->image;
        }

        if ($banner->update($data)) {
            return redirect()->route('.adminbanners.index')->with(['message' => 'Update banner successfully!']);
        }

        return redirect()->route('.adminbanners.index')->with(['error' => 'Update banner failed!']);
    }


    public function destroy(string $id)
    {
        $banner = Banner::find($id);

        if (!$banner) {
            return redirect()->route('.adminbanners.index')->with(['error' => 'Banner not exit!']);
        }

        $subStringImage = substr($banner->image, strlen(env('URL')));

        if ($subStringImage && file_exists($subStringImage)) {
            unlink($subStringImage);
        }

        $banner->delete();

        return back()->with(['message' => 'Delete successfully!']);
    }
}
