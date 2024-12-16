<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\ConversationMember;
use App\Models\Message;
use Exception;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function storeConversation(Request $request)
    {
        try {
            //validate sau
            //Tạo mối hộ thoại mới
            $conversation = Conversation::create([
                'name' => $request->name,
                'type' => $request->type,
                'course_id' => $request->course_id
            ]);
            if ($request->members == null) {
                return response()->json([
                    'status' => 201,
                    'message' => 'Tạo cuộc hội thoại thành công',
                    'data' => $conversation
                ]);
            }
            //Đi thêm các thành viên vào cuộc hội thoại
            foreach ($request->members as $memberId) {
                // $conversation->members()->attach($memberId);
                ConversationMember::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $memberId,
                    'is_owner' => false, // Quyền mặc định
                    'role' => 'student', // Vai trò mặc định
                ]);
            }
            return response()->json([
                'status' => 201,
                'message' => 'Tạo cuộc hội thoại thành công',
                'data' => $conversation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => "Lỗi : " . $e->getMessage(),
                'data' => []
            ]);
        }

    }
    public function getConversation(Request $request)
    {
        try {
            $idConversation = $request->id;
            $conversation = Conversation::with(['members:id,name,avatar,email', 'messages.sender:id,name,avatar,email'])->findOrFail($idConversation);
            return response()->json([
                'status' => 'success',
                'message' => "Lấy cuộc hội thoại thành công",
                'data' => $conversation
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => "Lỗi : " . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
    public function sendMessage(Request $request)
    {
        try {
            $validated = $request->validate([
                'conversation_id' => 'required|exists:conversations,id',
                'content' => 'required|string',
                'type' => 'in:text,image,file',
            ]);
            $message = Message::create([
                'conversation_id' => $validated['conversation_id'],
                'sender_id' => auth()->id(),
                'content' => $validated['content'],
                'type' => $validated['type'] ?? 'text',
            ]);

            // Phát sự kiện tới các thành viên
            broadcast(new \App\Events\MessageSent($message))->toOthers();
            return response()->json([
                'status' => 201,
                'message' => " Gửi tin nhắn thành công",
                'data' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => "Lỗi : " . $e->getMessage(),
                'data' => []
            ]);
        }

    }

}
