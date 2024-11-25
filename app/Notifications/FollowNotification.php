<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class FollowNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    protected $follow;
    protected $user;

    /**
     * Create a new notification instance.
     *
     * @param $follower
     */
    public function __construct($follow)
    {
        $this->follow = $follow;
        //Thằng được follow
        $this->user = User::find($follow->follower_id);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        // Bạn có thể thêm 'mail' nếu muốn gửi qua email
        return ['database', 'broadcast'];
    }

    /**
     * Store the notification data in the database.
     *
     * @param  mixed  $notifiable
     * @return array<string, mixed>
     */
    public function toDatabase($notifiable)
    {
        return [
            'type' => 'user_followed',
            'follower_id' => $this->user->id,
            'follower_name' => $this->user->name,
            'message' => "Thành viên {$this->user->name} đã theo dõi bạn.",
        ];
    }

    /**
     * Broadcast the notification.
     *
     * @param  mixed  $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'type' => 'user_followed',
            'follower_id' => $this->user->id,
            'follower_name' => $this->user->name,
            'message' => "Thành viên {$this->user->name} đã theo dõi bạn.",
        ]);
    }
}
