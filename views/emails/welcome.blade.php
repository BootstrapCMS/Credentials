@extends(Config::get('core.email'))

@section('content')
<p>Thank you for creating an account on <a href="{!! $url !!}">{!! Config::get('core.name') !!}</a>.</p>
@if (isset($link))
    <p>To activate your account, <a href="{!! $link !!}">click here</a>.</p>
@else
    <p>No account activation is required.</p>
@endif
@stop
