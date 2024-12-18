<?php

namespace App\Http\Controllers\api\Client;

use App\Events\ReadMessage;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ConversationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = auth()->user();

            $limit = $request->query('limit', 20);

            // Get tất cả cuộc trò chuyện của user logged in
            $conversations = Conversation::with(['members', 'messages'])
                ->whereHas('members', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->orderByDesc('updated_at')
                ->paginate($limit);

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
                $partner = $conversation->type == 'direct' ?
                    $conversation->members()
                        ->where('user_id', '<>', $user->id)
                        ->select(['users.avatar', 'users.name'])
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

                    // Rút gọn tên
                    $nameParts = explode(' ', $senderName);
                    if (count($nameParts) > 1) {
                        $senderName = $nameParts[0] . ' ' . $nameParts[1] . ' ...'; // Chỉ lấy 2 từ đầu tiên của tên
                    }

                    $lastMessage->content = "$senderName đã thu hồi một tin nhắn.";
                }

                $lastMessageContent = $lastMessage ? $lastMessage->content : ' ';
                if ($lastMessage && $lastMessage->sender_id == $user->id) {
                    $lastMessageContent = 'Bạn: ' . $lastMessageContent;
                }

                return [
                    'conversation_id' => $conversation->id,
                    'avatar' => $group->avatar ?? $partner->avatar,
                    'sender_id' => $lastMessage ? $lastMessage->sender_id : null,
                    'name' => $conversation->name ?? $partner->makeHidden('pivot')->name,
                    'type' => $conversation->type,
                    'last_message' => $lastMessageContent,
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
                'data' => [
                    'conversations' => $response,
                    'pagination' => [
                        'current_page' => $conversations->currentPage(),
                        'next_page_url' => $conversations->nextPageUrl(),
                        'prev_page_url' => $conversations->previousPageUrl(),
                        'per_page' => $conversations->perPage(),
                        'total' => $conversations->total(),
                        'last_page' => $conversations->lastPage(),
                    ]
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi lấy danh sách cuộc trò chuyện.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function showPrivateConversation(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            // id nguoi nhan
            $receiverId = $request->query('receiver_id');
            // id cuoc tro chuyen
            $conversationId = $request->query('conversation_id');

            // Tự nhắn cho bản thân
            if ($receiverId == $user->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Yêu cầu không hợp lệ.',
                    'data' => null,
                ], 400);
            }

            // Nếu truy cập bằng receiverId
            if ($receiverId) {
                $conversation = Conversation::with('members')
                    ->where('type', 'direct')
                    ->whereHas('members', function ($query) use ($user, $receiverId) {
                        $query->where('user_id', $user->id);
                    })
                    ->whereHas('members', function ($query) use ($receiverId) {
                        $query->where('user_id', $receiverId);
                    })
                    ->first();

                // Nếu chưa nhắn tin
                if (!$conversation) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Chưa có cuộc trò chuyện với người nhận này.',
                        'data' => null,
                    ], 204);
                }
            }
            // Nếu truy cập bằng conversationId
            else if ($conversationId) {
                // Check xem user có tham gia cuộc trò chuyện không
                $conversation = Conversation::with('members')
                    ->whereHas('members', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->find($conversationId);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Yêu cầu không hợp lệ.',
                    'data' => null,
                ], 400);
            }

            if (!$conversation) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn không có quyền truy cập đoạn chat này.',
                    'data' => [],
                ], 403);
            }

            // Check đối phương (người nhận)
            $partner = $conversation->type == 'direct' ?
                $conversation->members()
                    ->where('user_id', '<>', $user->id)
                    ->select(['users.id', 'users.name', 'users.avatar'])
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
            })->values()->map(function ($message) use ($user) {
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
                    'receiver_id' => $partner->id,
                    'receiver_avatar' => $partner->avatar,
                    'receiver_name' => $conversation->name ?? $partner->makeHidden('pivot')->name,
                    'type' => $conversation->type,
                    'messages' => $response,
                    'pagination' => [
                        'current_page' => $messages->currentPage(),
                        'next_page_url' => $messages->nextPageUrl(),
                        'prev_page_url' => $messages->previousPageUrl(),
                        'per_page' => $messages->perPage(),
                        'total' => $messages->total(),
                        'last_page' => $messages->lastPage(),
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

    public function readMessage($conversationId)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $conversation = Conversation::find($conversationId);

            if ($conversation) {
                $conversation->messages()
                    ->whereHas('receipts', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->first();
            }

            if (!$conversation) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không có quyền truy cập.',
                    'data' => ''
                ], 403);
            }

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

            // Lấy tin nhắn mới nhất từ cuộc trò chuyện
            $lastMessage = $conversation->messages()
                ->latest('created_at')
                ->first();

            // Trạng thái đọc của chính user đang đăng nhập
            $currentUserReceipt = $lastMessage->receipts->firstWhere('user_id', $user->id);

            // Trạng thái đọc của đối phương
            // $otherUserReceipt = $lastMessage->receipts->where('user_id', '!=', $user->id)->first();

            $latestMessage = [
                'id' => $lastMessage->id,
                'conversation_id' => $lastMessage->conversation_id,
                'type' => $lastMessage->type,
                'sender' => [
                    'id' => $lastMessage->sender->id,
                    'name' => $lastMessage->sender->name,
                    'avatar' => $lastMessage->sender->avatar,
                ],
                'is_read' => $currentUserReceipt ? $currentUserReceipt->is_read : 0,
                'read_at' => $currentUserReceipt ? $currentUserReceipt->read_at : null,
                // 'is_read_by_other' => $otherUserReceipt ? $otherUserReceipt->is_read : 0,
                // 'read_at_by_other' => $otherUserReceipt ? $otherUserReceipt->read_at : null,
                'created_at' => $lastMessage->created_at->format('Y-m-d H:i:s'),
                'deleted_at' => $lastMessage->deleted_at ? $lastMessage->deleted_at : null,
            ];

            broadcast(new ReadMessage($latestMessage));

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => '',
                'data' => $latestMessage,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi đọc tin nhắn.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
