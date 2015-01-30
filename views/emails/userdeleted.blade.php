@extends(Config::get('core.email'))

@section('content')
<p>You have deleted your account, and all your content, on <a href="{!! $url !!}">{!! Config::get('core.name') !!}</a>.</p>
<p>If this was not you, please contact us immediately.</p>
@stop
