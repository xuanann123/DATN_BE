<?php

namespace App\Notifications\Client\Instructor;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestWithdrawMoney extends Notification implements ShouldBroadcast
{
    use Queueable;

    protected $status;

    /**
     * Create a new notification instance.
     */
    public function __construct($status)
    {
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
                'type' => 'request_withdraw_money',
                'user_role' => 'instructor',
                'message' => $this->status == true ? "Yêu cầu rút tiền đã được duyệt" : "Yêu cầu rút tiền không được duyệt",
                'url' => 'http://localhost:5174/instructor/wallet'
            ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toBroadcast(object $notifiable): array
    {
        return [
            'type' => 'request_withdraw_money',
            'user_role' => 'instructor',
            'message' => $this->status == true ? "Yêu cầu rút tiền đã được duyệt" : "Yêu cầu rút tiền không được duyệt",
            'url' => 'http://localhost:5174/instructor/wallet'
        ];
    }
}
