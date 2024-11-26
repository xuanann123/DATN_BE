<?php

namespace App\Http\Controllers\api\Client;

use App\Http\Controllers\Controller;
use App\Models\QnA;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class QnAController extends Controller
{

    public function index(Request $request)
    {
        try {
            $status = $request->input('status');
            $user = auth()->user();
            $title = "Hỏi đáp trên hệ thống";
            $qnaHistory = QnA::when($status, function ($query, $status) use ($user) {
                match ($status) {
                    // 'all' => $query->where('user_id', $user->id),
                    'today' => $query->where('user_id', $user->id)->whereDate('created_at', today()),
                    'yesterday' => $query->where('user_id', $user->id)->whereDate('created_at', today()->subDay()),
                    'last_week' => $query->where('user_id', $user->id)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
                    'last_month' => $query->where('user_id', $user->id)->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]),
                    default => $query->where('user_id', $user->id)->whereDate('created_at', today())
                };
            })->get();

            //Lấy số lượng của nó 

            $count = [
                'today' => QnA::where("user_id", $user->id)->whereDate('created_at', today())->count(),
                'yesterday' => QnA::where("user_id", $user->id)->whereDate('created_at', today()->subDay())->count(),
                'last_week' => QnA::where("user_id", $user->id)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'last_month' => QnA::where("user_id", $user->id)->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            ];
            if (!$qnaHistory) {
                return response()->json([
                    'success' => false,
                    'message' => "Bạn chưa có lượt tìm kiếm nào hôm nay",
                    'data' => []
                ], 204);

            }
            return response()->json([
                'success' => true,
                'message' => "Danh sách CHAT OPENAI",
                'data' => $qnaHistory
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }

    }
    public function askQuestion(Request $request)
    {
        $question = $request->input('question');
        $apiKey = env('GEMINI_API_KEY');
        $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}";

        // Sử dụng Guzzle để gửi yêu cầu POST tới API
        $client = new Client();
        $response = $client->post($apiUrl, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                "contents" => [
                    [
                        "parts" => [
                            ["text" => $question]
                        ]
                    ]
                ]
            ]
        ]);


        // Lấy dữ liệu từ phản hồi
        $responseData = json_decode($response->getBody(), true);

        // Kiểm tra và lấy câu trả lời
        $answer = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? 'Không thể lấy câu trả lời';
        // Trả về câu trả lời dưới dạng JSON
        QnA::create([
            'user_id' => auth()->user()->id,
            'question' => $question,
            'answer' => $answer,
        ]);

        return response()->json(['answer' => $answer]);
    }
}
