<?php

namespace App\Console\Commands;

use App\Events\VoucherCreated;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ActivateVouchers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // Tên của command, để sử dụng khi chạy trong scheduler
    protected $signature = 'vouchers:activate';

    /**
     * The console command description.
     *
     * @var string
     */
    // Mô tả về công việc này
    protected $description = 'Kích hoạt vouchers đã đến thời gian start_time';
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Lấy thời gian hiện tại theo múi giờ Việt Nam
        $now = Carbon::now('Asia/Ho_Chi_Minh');

        // Lấy tất cả vouchers có thời gian start_time bằng với thời gian hiện tại
        $vouchers = Voucher::where('start_time', '<=', $now)
            ->where('is_active', 0)
            ->get();

        foreach ($vouchers as $voucher) {
            // Cập nhật trạng thái của voucher thành active
            $voucher->is_active = 1;
            $voucher->save();

            // Phát sự kiện VoucherCreated
            broadcast(new VoucherCreated($voucher))->toOthers();

            // Ghi log hoặc thông báo rằng voucher đã được kích hoạt
            $this->info("Voucher {$voucher->id} đã được kích hoạt.");
        }
        return Command::SUCCESS;
    }
}
