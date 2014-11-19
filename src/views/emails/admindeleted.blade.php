@extends(Config::get('graham-campbell/core::views.email'))

@section('content')
<p>An admin from <a href="{!! $url !!}">{!! Config::get('graham-campbell/core::platform.name') !!}</a> has deleted your account and all your content.</p>
<p>If this was unexpected, please contact us immediately.</p>
@stop
