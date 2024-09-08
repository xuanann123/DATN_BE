<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Vouchers\CreateVoucherRequest;
use App\Http\Requests\Admin\Vouchers\UpdateVoucherRequest;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{

    public function index()
    {
        $title = "Danh sách voucher";

        $vouchers = Voucher::query()->orderbyDesc('id')->paginate(10);

        return view('admin.vouchers.index', compact('vouchers', 'title'));
    }


    public function create()
    {
        $title = "Thêm mới voucher";
        return view('admin.vouchers.create', compact('title'));
    }


    public function store(CreateVoucherRequest $request)
    {
        $data = $request->all();

        $data['used_count'] = 0;

        if (!$request->is_active) {
            $data['is_active'] = 0;
        }

        $newVoucher = Voucher::query()->create($data);

        if (!$newVoucher) {
            return redirect()->route('.adminvouchers.index')->with(['error' => 'Create banner failed!']);
        }

        return redirect()->route('.adminvouchers.index')->with(['message' => 'Create banner successfully!']);
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
            return redirect()->route('.adminvouchers.index')->with(['error' => 'Voucher not exit!']);
        }

        if ($voucher->update($data)) {
            return redirect()->route('.adminvouchers.index')->with(['message' => 'Update voucher successfully!']);
        }

        return redirect()->route('.adminvouchers.index')->with(['error' => 'Update voucher failed!']);
    }


    public function destroy(string $id)
    {
        $voucher = Voucher::find($id);

        if (!$voucher) {
            return redirect()->route('.adminvouchers.index')->with(['error' => 'Voucher not exit!']);
        }

        if ($voucher->delete()) {
            return back()->with(['message' => 'Delete successfully!']);
        }

        return back()->with(['error' => 'Delete failed!']);
    }
}
