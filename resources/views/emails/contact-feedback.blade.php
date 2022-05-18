{{-- You can change this template using File > Settings > Editor > File and Code Templates > Code > Laravel Ideal Markdown Mail --}}
@component('mail::message')
# Hello {{$admin}}

You have a message from {{$contact['name']}}, <strong>{{$contact['email']}}</strong>.
<hr>
<p>{{$contact['message']}}</p>

<hr>
Thanks,<br>
{{ config('app.name') }}
@endcomponent
