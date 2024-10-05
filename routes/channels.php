<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
//Gửi voucher tạo ra cho các user toàn bộ hệ thống 
Broadcast::channel('vouchers', function () {
    //Vì là kênh công khai nên auto trả về true
    return true;
});
//Yêu cầu phải login
Broadcast::channel('chat', function ($user) {
    if ($user) {
        return [
            'name' => $user->name,
            'id' => $user->id,
            'avatar' => $user->avatar
        ];
    }
    return false;
});