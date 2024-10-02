<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VoucherCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    //Muốn sử dụng ở khắp mọi nơi thì để public
    public $voucher;
    public function __construct($voucher)
    {
        $this->voucher = $voucher;
    }


    public function broadcastOn()   
    {
        //publish chanel
        return new Channel('vouchers');
    }
    public function broadcastWith()
    {
        //Trả dữ liệu này về bên phía blade client
        return [
            'name' => $this->voucher->name,
            'code' => $this->voucher->code,
            'description' => $this->voucher->description,
            'discount' => $this->voucher->discount,
            'start_time' => $this->voucher->start_time,
            'end_time' => $this->voucher->end_time,
        ];
    }
}
