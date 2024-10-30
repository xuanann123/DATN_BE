<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Từ Chối Khóa Học</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #ffffff;
        }

        .container {
            max-width: 650px;
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

        .conditions {
            margin-left: 20px;
            padding: 10px;
            border-left: 4px solid #007bff;
        }

        .button {
            display: inline-block;
            padding: 10px 15px;
            color: #fff;
            background: #007bff;
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
        <h1>Từ chối khóa học{{ $course->title }}</h1>

        <p>Xin chào {{ $course->user->name }},</p>

        <p>Chúng tôi rất tiếc phải thông báo rằng khóa học của bạn đã bị từ chối vì những điều kiện sau không đạt yêu
            cầu:</p>

        @if (collect($conditions)->contains(fn($condition) => !$condition['status']))
            <div class="conditions">
                @foreach ($conditions as $condition)
                    @if (!$condition['status'])
                        <p>
                            <strong>{{ $condition['label'] }}</strong>:
                            {{ $condition['value'] }}/{{ $condition['required'] }}
                        </p>
                    @endif
                @endforeach
            </div>
            <br>
        @endif

        @if ($course->admin_comments)
            <strong>Lí do:</strong>
            <div>{!! $course->admin_comments !!}</div>
        @endif

        <p>Chúng tôi khuyến khích bạn xem xét và cải thiện các điều kiện này trước khi gửi lại khóa học.</p>

        <div class="footer">
            <p>Nếu bạn có bất kỳ câu hỏi nào, đừng ngần ngại liên hệ với chúng tôi.</p>
            <p>Cảm ơn bạn đã sử dụng hệ thống của chúng tôi!</p>
            <p>Trân trọng,<br>{{ config('app.name') }}</p>
        </div>
    </div>
</body>

</html>
