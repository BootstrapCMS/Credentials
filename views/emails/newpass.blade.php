@extends(Config::get('core.email'))

@section('content')
<p>The password for your account on <a href="{!! $url !!}">{!! Config::get('core.name') !!}</a> has just been changed.</p>
<p>If this was not you, please contact us immediately.</p>
@stop
