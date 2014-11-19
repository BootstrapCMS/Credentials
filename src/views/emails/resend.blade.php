@extends(Config::get('graham-campbell/core::views.email'))

@section('content')
<p>You have requested we resend the activation link for <a href="{!! $url !!}">{!! Config::get('graham-campbell/core::platform.name') !!}</a>.</p>
<p>To activate your account, <a href="{!! $link !!}">click here</a>.</p>
@stop
