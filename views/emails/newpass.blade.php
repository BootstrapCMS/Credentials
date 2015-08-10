@extends(Config::get('credentials.email'))

@section('content')
<p>The password for your account on <a href="{!! $url !!}">{!! Config::get('app.name') !!}</a> has just been changed.</p>
<p>If this was not you, please contact us immediately.</p>
@stop
