<?php

namespace App\Http\Controllers\api\Client;

use App\Events\MessageSent;
use App\Models\User;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Models\MessageReceipt;
use App\Models\ConversationMember;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class MessageController extends Controller
{
    public function sendPrivateMessage(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user(); // user logged in
            $receiverId = $request->receiver_id; // id người nhận
            $messageContent = $request->message; // msg content

            if ($receiverId == $user->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Thao tác không hợp lệ.',
                    'data' => [],
                ], 400);
            }

            // check cuộc trò chuyện giữa người đăng nhập và người nhận đã tồn tại chưa
            $conversation = Conversation::query()
                ->whereHas('members', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->whereHas('members', function ($query) use ($receiverId) {
                    $query->where('user_id', $receiverId);
                })
                ->first();

            // Nếu chưa có cuộc trò chuyện thì create
            if (!$conversation) {
                // Tạo cuộc trò chuyện mới
                $conversation = Conversation::create([
                    'name' => null,
                    'type' => 'direct', // Chat 1:1
                    'is_active' => 1,
                ]);

                // Add thành viên vào cuộc trò chuyện
                $this->addConversationMember($conversation->id, $user->id);
                $this->addConversationMember($conversation->id, $receiverId);
            }

            // Lưu tin nhắn
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'content' => $messageContent,
                'type' => 'text',
            ]);

            $conversation->update([
                'last_message_id' => $message->id,
            ]);

            $this->createMessageReceipts($message->id, $user->id, $receiverId);

            // sent event
            broadcast(new MessageSent($message));

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Tin nhắn đã được gửi thành công.',
                'data' => $message->load('sender:id,name,avatar'),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi gửi tin nhắn.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteMessage(Request $request, $messageId)
    {
        DB::beginTransaction();

        try {
            $user = auth()->user();

            $messageReceipt = MessageReceipt::where('message_id', $messageId)
                ->where('user_id', $user->id)
                ->first();

            // Nếu không tồn tại receipt
            if (!$messageReceipt) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Thao tác không hợp lệ.',
                    'data' => [],
                ], 400);
            }

            // check del
            if ($messageReceipt->deleted_at !== null || $messageReceipt->message->deleted_at !== null) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tin nhắn đã được xóa trước đó.',
                    'data' => [],
                ], 400);
            }

            $messageReceipt->update([
                'deleted_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Tin nhắn đã được xóa thành công.',
                'data' => [
                    'message_id' => $messageReceipt->message_id,
                    'conversation_id' => $messageReceipt->message->conversation_id,
                    'user_id' => $messageReceipt->user_id,
                    'action' => 'delete_message',
                    'deleted_at' => $messageReceipt->deleted_at->format('Y-m-d H:i:s'),
                ],
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi xóa tin nhắn.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function unsendMessage(Request $request, $messageId)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $message = Message::find($messageId);

            // Check tồn tại
            if (!$message) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tin nhắn không tồn tại.',
                    'data' => [],
                ], 404);
            }

            // Chỉ cho người gửi thu hồi
            if ($message->sender_id !== $user->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Thao tác không hợp lệ.',
                    'data' => [],
                ], 400);
            }

            //
            if ($message->deleted_at !== null) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tin nhắn đã được thu hồi trước đó.',
                    'data' => [],
                ], 400);
            }

            // Xóa
            $message->update([
                'deleted_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Tin nhắn đã được thu hồi thành công.',
                'data' => [
                    'message_id' => $message->id,
                    'type' => $message->type,
                    'sender_id' => $message->sender_id,
                    'is_unsend' => 1,
                    'conversation_id' => $message->conversation_id,
                    'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                    'deleted_at' => $message->deleted_at,
                ],
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi thu hồi tin nhắn.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // Add thanh vien vao cuoc tro chuyen
    private function addConversationMember($conversationId, $userId)
    {
        ConversationMember::create([
            'conversation_id' => $conversationId,
            'user_id' => $userId,
            'role' => null,
            'joined_at' => now(),
        ]);
    }

    // Trạng thái của tin nhắn
    private function createMessageReceipts(int $messageId, int $senderId, int $receiverId): void
    {
        MessageReceipt::create([
            'message_id' => $messageId,
            'user_id' => $senderId,
            'is_read' => 1,
            'read_at' => now(),
        ]);

        MessageReceipt::create([
            'message_id' => $messageId,
            'user_id' => $receiverId,
            'is_read' => 0,
        ]);
    }

}
