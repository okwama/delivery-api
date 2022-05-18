{{-- You can change this template using File > Settings > Editor > File and Code Templates > Code > Laravel Ideal Markdown Mail --}}
@component('mail::message')
# Dear {{ucfirst($order['customer_name'])}}

#### We have received you order.
<hr>
<p>The order no. is #<b>{{$order['orderNo'] ?? ''}}</b></p>
<p>You can contact us via <b>+254714798820</b> or <b>info@drinksdeliverykenya.co.ke</b></p>
<hr>
Thanks,<br>
{{ config('app.name') }}
@endcomponent
