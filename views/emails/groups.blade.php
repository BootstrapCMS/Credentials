@extends(Config::get('credentials.email'))

@section('content')
<p>An admin from <a href="{!! $url !!}">{!! Config::get('app.name') !!}</a> has changed your group memberships.</p>
<p>Login to see your updated permissions.</p>
@stop
