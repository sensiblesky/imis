@component('mail::message')
# Two-Factor Authentication OTP

Hi {{ $firstname }},

Here is your OTP code for Two-Factor Authentication:

@component('mail::panel')
**OTP Code:** {{ $otp }}
@endcomponent

If you did not request this, please ignore this email or contact support.

Thanks,  
{{ config('app.name') }}
@endcomponent
