<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Tags\CreateTagsRequest;
use App\Http\Requests\Admin\Tags\UpdateTagRequest;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $title = "Danh sách thẻ";
        $keyword = "";
        if ($request->input('keyword')) {
            $keyword = $request->input('keyword');
        }
        $status = $request->query('status', 'all');
        // Khởi tạo listAct ban đầu
        $listAct = [
            "trash" => "Xoá toàn bộ",
        ];
        // Lọc tags và đồng thời thay đổi giá trị listAct
        $tags = Tag::when($status != 'all', function ($query) use ($status, &$listAct) {
            match ($status) {
                'trash' => $query->onlyTrashed() && $listAct = [
                    "restore" => "Khôi phục toàn bộ",
                    "forceDelete" => "Xoá cứng toàn bộ",
                ],
                default => null
            };
        })->where('name', 'like', "%$keyword%")->latest("id")->paginate(10);
        $count = [
            'all' => Tag::count(),
            'trash' => Tag::onlyTrashed()->count(),
        ];
        return view('admin.tags.index', compact('title', 'tags','listAct','count'));
    }
    public function create()
    {
        $title = "Thêm mới thẻ";
        return view('admin.tags.create', compact('title'));
    }
    public function store(CreateTagsRequest $request)
    {
        $data = $request->all();
        Tag::create($data);
        return redirect()->route('admin.tags.index')->with(['success' => 'Thêm mới thẻ thành công']);
    }
    public function edit(Tag $tag)
    {
        $title = "Cập nhật thẻ";
        return view('admin.tags.edit', compact('title', 'tag'));
    }
    public function update(Tag $tag, Request $request)
    {
        $data = $request->all();
        $tag->update($data);
        return redirect()->route('admin.tags.index')->with(['success' => 'Cập nhật thẻ thành công']);
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();
        return redirect()->route('admin.tags.index')->with(['success' => 'Xóa thẻ thành công']);
    }

    public function action(Request $request)
    {
        $listCheck = $request->listCheck;
        if (!$listCheck) {
            return redirect()->route("admin.tags.index")->with('error', 'Vui lòng chọn danh mục cần thao tác');
        }
        $act = $request->act;
        if (!$act) {
            return redirect()->route("admin.tags.index")->with('error', 'Vui lòng chọn hành động để thao tác');
        }
        $message = match ($act) {
            'trash' => function () use ($listCheck) {
                    Tag::destroy($listCheck);
                    return 'Xoá thành công toàn bộ bản ghi đã chọn';
                },
            'restore' => function () use ($listCheck) {
                    Tag::onlyTrashed()->whereIn("id", $listCheck)->restore();
                    return 'Khôi phục thành công toàn bộ bản ghi';
                },
            'forceDelete' => function () use ($listCheck) {
                    Tag::onlyTrashed()->whereIn("id", $listCheck)->forceDelete();
                    return 'Xoá vĩnh viễn toàn bộ bản ghi khỏi hệ thống';
                },
            default => fn() => 'Hành động không hợp lệ',
        };
        return redirect()->route("admin.tags.index")->with('success', $message());
    }


    public function restore(string $id)
    {
        $tag = Tag::onlyTrashed()->find($id);
        if (!$tag) {
            return redirect()->route('admin.tags.index')->with(['error' => 'Tag không tồn tại!']);
        }
        $tag->restore();
        return redirect()->route('admin.tags.index')->with(['success' => 'Khôi phục thành công!']);
    }

    public function forceDelete(string $id)
    {
        $tag = Tag::onlyTrashed()->find($id);
        //Nếu Tag đó không tồn tại thì báo lỗi
        if (!$tag) {
            return redirect()->route('admin.tags.index')->with(['error' => 'Tag không tồn tại!']);
        }
        $tag->forceDelete();
        return redirect()->route('admin.tags.index')->with(['success' => 'Xoá thành công']);
    }



}
