<?php

namespace App\Http\Controllers\Admin;

use App\Events\VoucherCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Vouchers\CreateVoucherRequest;
use App\Http\Requests\Admin\Vouchers\UpdateVoucherRequest;
use App\Models\Category;
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
        //Lấy danh mục là key và toàn bộ mảng là khoá học bên trong
        $listCourse = Category::with( ['courses' => function ($query) { $query->where('is_active', 1)->where('status', 'approved');} ])->get();
        // dd($listCourse);
        return view('admin.vouchers.create', compact('title', 'listCourse'));
    }


    public function store(CreateVoucherRequest $request)
    {
        $data = $request->all();

        $data['used_count'] = 0;
        $data['is_active'] = $request->is_active ?? 0;
        $data['is_private'] = $request->is_private ?? 0;
        // dd($data);

        DB::beginTransaction(); // Bắt đầu transaction

        try {
            // Tạo voucher mới
            $newVoucher = Voucher::create([
                'name' => $data['name'],
                'code' => $data['code'],
                'description' => $data['description'],
                'type' => $data['type'],
                'discount' => $data['discount'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'count' => $data['count'],
                'used_count' => $data['used_count'],
                'is_active' => $data['is_active'],
                'is_private' => $data['is_private']
            ]);

            // Nếu là voucher riêng tư, thêm liên kết với khóa học
            if ($data['is_private'] == 1 && isset($data['id_course'])) {
                $newVoucher->courses()->attach($data['id_course']);
            }

            // Gửi sự kiện realtime
            event(new VoucherCreated($newVoucher));

            DB::commit(); // Xác nhận transaction
            return redirect()->route('admin.vouchers.index')->with('success', 'Thêm voucher thành công');
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback nếu có lỗi
            return redirect()->back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }




    public function edit(string $id)
    {
        $title = "Chỉnh sửa voucher";
        $voucher = Voucher::find($id);
        $listCourse = Category::with(['courses' => function ($query) {
            $query->where('is_active', 1)->where('status', 'approved'); }])->get();

        //Lấy danh sách khoá voucher trong này ra
       $listVoucherCourse = $voucher->courses()->where("id_voucher", $id)->get()->pluck('id')->toArray();

        return view('admin.vouchers.edit', compact('voucher', 'title', 'listCourse', 'listVoucherCourse'));
    }


    public function update(UpdateVoucherRequest $request, string $id)
    {

        $data = $request->all();
        // dd($data);

        if (!$request->is_active) {
            $data['is_active'] = 0;
        }
        if (!$request->id_course) {
            $data['id_course'] = 0;
        }

        if (!$request->is_private) {
            $data['is_private'] = 0;
        }

        $voucher = Voucher::find($id);

        $data['used_count'] = $voucher->used_count;

        if (!$voucher) {
            return redirect()->route('admin.vouchers.index')->with(['error' => 'Mã giảm giá không tồn tại!']);
        }

        if ($voucher->update($data)) {
            //Đi cập nhật dữ liệu nếu có 
            if($data['is_private'] == 1 && isset($data['id_course'])) {
                $voucher->courses()->sync($data['id_course']);
            }
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
