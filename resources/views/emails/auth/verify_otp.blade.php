@component('mail::message')
# Chào bạn,

Mã OTP của bạn để xác thực tài khoản là:

@component('mail::button', ['url' => ''])
    {{ $otpCode }}
@endcomponent

Mã OTP này sẽ hết hạn sau 15 phút.

Nếu bạn gặp bất kỳ vấn đề gì, vui lòng liên hệ với chúng tôi qua email [support@hn_85.com](mailto:support@hn_85.com).


Cảm ơn,<br>
{{ config('app.name') }}

@component('mail::panel')
Đây là một email tự động, vui lòng không trả lời.
@endcomponent
@endcomponent
