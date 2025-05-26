@component('mail::message')
# Login Alert

Hi {{ $data['name'] }},

We noticed a login to your account at **{{ $data['time'] }}**.

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

If this wasn't you, please update your password immediately.

Thanks,  
{{ config('app.name') }}
@endcomponent
