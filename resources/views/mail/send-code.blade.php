@component('mail::message')
# Introduction

Your Verification Code is :
{{ $token }}

@component('mail::button', ['url' => ''])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
