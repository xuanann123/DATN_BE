<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Banners\CreateBannerRequest;
use App\Http\Requests\Admin\Banners\UpdateBannerRequest;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{

    public function index()
    {
        $title = "Danh sách banner";
        $banners = Banner::query()->orderbyDesc('id')->paginate(10);
        return view('admin.banners.index', compact('banners', 'title'));
    }


    public function create()
    {
        $title = "Thêm mới banner";
        return view('admin.banners.create', compact('title'));
    }


    public function store(CreateBannerRequest $request)
    {

        $data = $request->except('image');

        if ($request->image && $request->hasFile('image')) {
            $pathImage = Storage::putFile('banners', $request->file('image'));
            $fullNameImage = env('URL') . 'storage/' . $pathImage;

            $data['image'] = $fullNameImage;
        }

        $newBanner = Banner::query()->create($data);

        if (!$newBanner) {
            return redirect()->route('.adminbanners.index')->with(['error' => 'Create banner failed!']);
        }

        return redirect()->route('.adminbanners.index')->with(['message' => 'Create banner successfully!']);
    }

    public function edit(string $id)
    {
        $title = "Chỉnh sửa banner";
        $banner = Banner::find($id);

        if (!$banner) {
            return back()->with(['error' => 'Banner not exit!']);
        }

        return view('admin.banners.edit', compact('banner', 'title'));
    }


    public function update(UpdateBannerRequest $request, string $id)
    {

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
