@extends(Config::get('core.email'))

@section('content')
<p>You have requested we resend the activation link for <a href="{!! $url !!}">{!! Config::get('core.name') !!}</a>.</p>
<p>To activate your account, <a href="{!! $link !!}">click here</a>.</p>
@stop
