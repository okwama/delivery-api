{{-- You can change this template using File > Settings > Editor > File and Code Templates > Code > Laravel Ideal Markdown Mail --}}
@component('mail::message')
# Hello {{ucfirst($admin)}}

#### {{ucfirst($order['customer_name'])}} has made an order.
<hr>
<p>The order no. is #<b>{{$order['orderNo'] ?? ''}}</b> and it will be delivered to {{$order['location'] ?? 'Nairobi'}}</p>
<p>You can contact the customer via <b>{{$order['phone'] ?? ''}}</b> or <b>{{$order['customer_email'] ?? ''}}</b></p>
<hr>
Thanks,<br>
{{ config('app.name') }}
@endcomponent
