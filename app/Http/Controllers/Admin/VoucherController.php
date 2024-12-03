<?php

namespace App\Http\Controllers\Admin;

use App\Events\VoucherCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Vouchers\CreateVoucherRequest;
use App\Http\Requests\Admin\Vouchers\UpdateVoucherRequest;
use App\Models\Course;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{

    public function index(Request $request)
    {
        $title = "Danh sách voucher";
        $status = $request->query('status', 'all');
        // Khởi tạo listAct ban đầu
        $listAct = [
            "active" => "Hoạt động tất cả",
            "inactive" => "Tắt hoạt động tất cả",
            "trash" => "Xoá toàn bộ",
        ];
        // Lọc banners và đồng thời thay đổi giá trị listAct
        $vouchers = Voucher::when($status != 'all', function ($query) use ($status, &$listAct) {
            match ($status) {
                'active' => $query->where('is_active', 1) && $listAct = [
                    "inactive" => "Tắt hoạt động tất cả",
                    "trash" => "Xoá toàn bộ",
                ],
                'inactive' => $query->where('is_active', 0) && $listAct = [
                    "active" => "Hoạt động tất cả",
                    "trash" => "Xoá toàn bộ",
                ],
                'trash' => $query->onlyTrashed() && $listAct = [
                    "restore" => "Khôi phục toàn bộ",
                    "forceDelete" => "Xoá cứng toàn bộ",
                ],
                default => null
            };
        })->latest("id")->paginate(10);

        $count = [
            'all' => Voucher::count(),
            'active' => Voucher::where('is_active', 1)->count(),
            'inactive' => Voucher::where('is_active', 0)->count(),
            'trash' => Voucher::onlyTrashed()->count(),
        ];

        return view('admin.vouchers.index', compact('vouchers', 'title', 'count', "listAct"));
    }


    public function create()
    {
        $title = "Thêm mới voucher";
        //Lấy danh sách khóa học đang được hoạt động
        $courses = Course::select('id',  'name','thumbnail')
                ->where('is_active', 1)
                ->where('status', 'approved')
                ->latest('id')
                ->get();

        return view('admin.vouchers.create', compact('title','courses'));
    }


    public function store(CreateVoucherRequest $request)
    {
        // dd($request->all());
        $data = $request->all();

        $data['used_count'] = 0;

        if (!$request->is_active) {
            $data['is_active'] = 0;

        }
        if (!$request->is_publish) {
            $data['is_publish'] = 0;
        }
        try {
            //Thêm dữ liệu
            $newVoucher = Voucher::query()->create($data);
            //Thêm dữ liệu bảng trung gian
        // if($request->course_id) {

        //     $newVoucher->courses()->attach($request->course_id);
        // }

            //realtime voucher
            event(new VoucherCreated($newVoucher));


            return redirect()->route('admin.vouchers.index')->with(['success' => 'Thêm voucher thành công']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }

    }



    public function edit(string $id)
    {
        $title = "Chỉnh sửa voucher";
        $voucher = Voucher::find($id);
        return view('admin.vouchers.edit', compact('voucher', 'title'));
    }


    public function update(UpdateVoucherRequest $request, string $id)
    {

        $data = $request->all();

        if (!$request->is_active) {
            $data['is_active'] = 0;
        }

        $voucher = Voucher::find($id);

        $data['used_count'] = $voucher->used_count;

        if (!$voucher) {
            return redirect()->route('admin.vouchers.index')->with(['error' => 'Mã giảm giá không tồn tại!']);
        }

        if ($voucher->update($data)) {
            return redirect()->route('admin.vouchers.index')->with(['message' => 'Cập nhật thành công!']);
        }

        return redirect()->route('admin.vouchers.index')->with(['error' => 'Cập nhật thất bại!']);
    }


    public function destroy(string $id)
    {
        $voucher = Voucher::find($id);

        if (!$voucher) {
            return redirect()->route('admin.vouchers.index')->with(['error' => 'Mã giảm giá không tồn tại!']);
        }

        if ($voucher->delete()) {
            return back()->with(['message' => 'Xóa thành công!']);
        }

        return back()->with(['error' => 'Xóa thất bại!']);
    }
}
