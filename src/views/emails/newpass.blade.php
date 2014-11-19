@extends(Config::get('graham-campbell/core::views.email'))

@section('content')
<p>The password for your account on <a href="{!! $url !!}">{!! Config::get('graham-campbell/core::platform.name') !!}</a> has just been changed.</p>
<p>If this was not you, please contact us immediately.</p>
@stop
