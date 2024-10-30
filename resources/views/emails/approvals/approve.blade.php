<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khóa Học Được Chấp Thuận</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 1px solid;
        }
        h1 {
            color: #333;
        }
        .button {
            display: inline-block;
            padding: 10px 15px;
            color: #fff;
            background: #28a745;
            text-decoration: none;
            border-radius: 5px;
        }
        .footer {
            margin-top: 20px;
            font-size: 0.9em;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Khóa học đã được chấp thuận: {{ $course->name }}</h1>

        <p>Xin chào {{ $course->user->name }},</p>

        <p>Chúng tôi vui mừng thông báo rằng khóa học của bạn đã được chấp thuận!</p>

        <p>Cảm ơn bạn đã đóng góp vào nền tảng học tập của chúng tôi. Chúng tôi hy vọng khóa học này sẽ mang lại giá trị cho người học.</p>

        <div class="footer">
            <p>Nếu bạn có bất kỳ câu hỏi nào, đừng ngần ngại liên hệ với chúng tôi.</p>
            <p>Cảm ơn bạn đã sử dụng hệ thống của chúng tôi!</p>
            <p>Trân trọng,<br>{{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>
