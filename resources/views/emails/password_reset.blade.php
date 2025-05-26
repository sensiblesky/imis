@component('mail::message')
# Password Reset Request

Hi {{ $data['name'] }},

We received a request to reset your password at **{{ $data['time'] }}**.

- **IP Address:** {{ $data['ip'] }}
@isset($data['platform'])
- **Platform:** {{ $data['platform'] }}
@endisset
@isset($data['device'])
- **Device:** {{ $data['device'] }}
@endisset
@isset($data['city'])
- **Location:** {{ $data['city'] }}, {{ $data['country'] ?? '' }}
@endisset

If you made this request, click the button below to reset your password:

@component('mail::button', ['url' => $data['reset_url']])
Reset Password
@endcomponent

If you didn’t request a password reset, you can safely ignore this email.

Thanks,  
{{ config('app.name') }}
@endcomponent
