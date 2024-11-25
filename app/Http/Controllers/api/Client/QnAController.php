<?php

namespace App\Http\Controllers\api\Client;

use App\Http\Controllers\Controller;
use App\Models\QnA;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class QnAController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $title = "Hỏi đáp trên hệ thống";
        // Retrieve all questions and answers from the database
        $qnaHistory = QnA::where('user_id', $user->id)->get();
        // Return the view with history data
        return view('admin.qna.index', compact('title', 'qnaHistory'));
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
