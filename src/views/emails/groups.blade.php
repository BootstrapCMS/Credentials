@extends(Config::get('views.email', 'layouts.email'))

@section('content')
<p>An admin from <a href="{!! $url !!}">{!! Config::get('platform.name') !!}</a> has changed your group memberships.</p>
<p>Login to see your updated permissions.</p>
@stop
