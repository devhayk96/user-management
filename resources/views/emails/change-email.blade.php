<x-mail::message>
# Verify email

<br>Your verification code is: {{$code}}

Click the following button to verify your new email: {{$url}}

<x-mail::button :url="'{{$url}}'">
Verify
</x-mail::button>

If you don't see the button or can't click it, you can copy and paste this link into your browser - {{ $url }}

Made by Hayk
</x-mail::message>
