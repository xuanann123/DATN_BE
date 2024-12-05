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
        <h1>Xin cám ơn {{ $user->name }}, bạn đã bỏ thời gian và công sức trở</h1>
        <p> Mong bạn ứng tuyển vào ngày khác, và có thể trở thành giảng viên của hệ thống</p>
        <hr>
        <p>Lý do: {{ $admin_comments->admin_comments }}</p>
    </div>
</body>

</html>
