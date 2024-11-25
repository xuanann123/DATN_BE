<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI QnA với Laravel</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>AI QnA với Laravel</h1>
    <div>
        <input type="text" id="questionInput" placeholder="Nhập câu hỏi của bạn..." />
        <button onclick="askQuestion()">Hỏi</button>
    </div>
    <div>
        <h2>Câu trả lời:</h2>
        <div id="answerOutput"></div>
    </div>

    <script>
        async function askQuestion() {
            const question = document.getElementById('questionInput').value;
            const answerOutput = document.getElementById('answerOutput');

            // Hiển thị thông báo chờ
            answerOutput.innerText = "Đang xử lý...";

            try {
                const response = await fetch('/admin/qna/ask', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ question })
                });

                const data = await response.json();
                answerOutput.innerText = data.answer;

            } catch (error) {
                console.error('Error:', error);
                answerOutput.innerText = "Đã có lỗi xảy ra khi xử lý câu hỏi.";
            }
        }
    </script>
</body>
</html>
