<?php

namespace App\Http\Controllers\api\Client;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ConversationController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();

            // Get tất cả cuộc trò chuyện của user logged in
            $conversations = Conversation::with(['members', 'messages'])
                ->whereHas('members', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->orderByDesc('updated_at')
                ->get();

            $response = $conversations->map(function ($conversation) use ($user) {
                $lastMessage = $conversation->messages->last(); // lấy tin nhắn cuối cùng
                $unreadMessagesCount = $lastMessage ?
                    $conversation->messages()->whereHas('receipts', function ($query) use ($user) {
                        $query->where('user_id', $user->id)
                            ->where('is_read', 0)
                            ->where('deleted_at', null);
                    })->count() : '';

                // Check role của user trong cuộc trò chuyện
                $member = $conversation->members->firstWhere('user_id', $user->id);
                $role = $member ? $member->role : null;
                // Tên đối phương nếu là chat riêng
                $partnerName = $conversation->type == 'direct' ?
                    $conversation->members()
                        ->where('user_id', '<>', $user->id)
                        ->select('name')
                        ->first() : (object) ['name' => 'Đoạn Chat'];
                // Trạng thái tin nhắn
                $readStatus = $lastMessage->receipts()
                    ->where('user_id', $user->id)
                    ->where('is_read', 1)
                    ->first();
                // Tin nhắn đã thu hồi
                $deletedMessage = $lastMessage && $lastMessage->deleted_at ? $lastMessage : null;
                if ($deletedMessage) {
                    $senderName = $deletedMessage->sender_id == $user->id ? "Bạn" : $deletedMessage->sender->name;
                    $lastMessage->content = "$senderName đã thu hồi một tin nhắn.";
                }

                return [
                    'conversation_id' => $conversation->id,
                    'name' => $conversation->name ?? $partnerName->makeHidden('pivot')->name,
                    'type' => $conversation->type,
                    'last_message' => $lastMessage ? $lastMessage->content : ' ',
                    'last_message_time' => $lastMessage ? $lastMessage->created_at->format('Y-m-d H:i:s') : null,
                    'is_read' => $readStatus ? 1 : 0,
                    'unread_messages_count' => $unreadMessagesCount,
                    'user_role' => $role,
                    'is_active' => $conversation->is_active,
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Danh sách cuộc trò chuyện.',
                'data' => $response,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi lấy danh sách cuộc trò chuyện.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function showPrivateConversation($conversationId, Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();

            // Check xem user có tham gia cuộc trò chuyện không
            $conversation = Conversation::with('members')
                ->whereHas('members', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->find($conversationId);

            if (!$conversation) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn không có quyền truy cập đoạn chat này.',
                    'data' => [],
                ], 403);
            }

            // Tên đối phương nếu là chat riêng
            $partnerName = $conversation->type == 'direct' ?
                $conversation->members()
                    ->where('user_id', '<>', $user->id)
                    ->select('name')
                    ->first() : (object) ['name' => 'Đoạn Chat'];

            // Lấy list msg trong cuộc trò chuyện
            $limit = $request->query('limit', 20);
            $messages = $conversation->messages()
                ->with([
                    'sender:id,name,avatar',
                    'receipts',
                ])
                ->orderByDesc('created_at')
                ->paginate($limit); // paginate

            // Đánh dấu tin nhắn là đã đọc (nếu chưa đọc)
            $conversation->messages()
                ->whereHas('receipts', function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->where('read_at', null);
                })
                ->each(function ($message) use ($user) {
                    // update những tin nhắn chưa đọc
                    $message->receipts()
                        ->where('user_id', $user->id)
                        ->update([
                            'is_read' => 1,
                            'read_at' => now(),
                        ]);
                });

            // chuẩn hóa resp
            $response = $messages->filter(function ($message) use ($user) {
                // Check nếu người dùng hiện tại đã xóa tin nhắn
                $currentUserReceipt = $message->receipts
                    ->firstWhere('user_id', $user->id);

                // Loại bỏ tin nhắn nếu người dùng hiện tại đã xóa ở phía mình
                return !$currentUserReceipt || is_null($currentUserReceipt->deleted_at);
            })->map(function ($message) use ($user) {
                // Tin nhắn đã thu hồi
                $deletedMessage = $message && $message->deleted_at ? $message : null;
                if ($deletedMessage) {
                    $senderName = $deletedMessage->sender_id == $user->id ? "Bạn" : $deletedMessage->sender->name;
                    $message->content = "$senderName đã thu hồi một tin nhắn.";
                }
                // Trạng thái đọc của chính user đang đăng nhập
                $currentUserReceipt = $message->receipts->firstWhere('user_id', $user->id);

                // Trạng thái đọc của đối phương
                $otherUserReceipt = $message->receipts->where('user_id', '!=', $user->id)->first();
                
                return [
                    'message_id' => $message->id,
                    'content' => $message ? $message->content : ' ',
                    'type' => $message->type,
                    'sender' => [
                        'id' => $message->sender->id,
                        'name' => $message->sender->name,
                        'avatar' => $message->sender->avatar,
                    ],
                    'is_read_by_user' => $currentUserReceipt ? $currentUserReceipt->is_read : 0,
                    'read_at_by_user' => $currentUserReceipt ? $currentUserReceipt->read_at : null,
                    'is_read_by_other' => $otherUserReceipt ? $otherUserReceipt->is_read : 0,
                    'read_at_by_other' => $otherUserReceipt ? $otherUserReceipt->read_at : null,
                    'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                    'deleted_at' => $message->deleted_at ? $message->deleted_at : null,
                ];
            });

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Nội dung cuộc trò chuyện.',
                'data' => [
                    'conversation_id' => $conversation->id,
                    'name' => $conversation->name ?? $partnerName->makeHidden('pivot')->name,
                    'type' => $conversation->type,
                    'messages' => $response,
                    'pagination' => [
                        'current_page' => $messages->currentPage(),
                        'last_page' => $messages->lastPage(),
                        'total' => $messages->total(),
                    ],
                ],
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi truy cập nội dung cuộc trò chuyện.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
