@component('mail::message')
# Chào bạn!

Vui lòng xác thực địa chỉ email của bạn bằng cách nhấp vào liên kết bên dưới:

@component('mail::button', ['url' => $verifyUrl, 'color' => 'primary'])
Xác Thực Email
@endcomponent

Nếu bạn gặp bất kỳ vấn đề gì, vui lòng liên hệ với chúng tôi qua email [support@hn_85.com](mailto:tuwtf2605@gmail.com).

Cảm ơn bạn!

@component('mail::panel')
    Đây là một email tự động, vui lòng không trả lời.
@endcomponent

@endcomponent
