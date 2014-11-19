@extends(Config::get('graham-campbell/core::email'))

@section('content')
<p>An admin from <a href="{!! $url !!}">{!! Config::get('graham-campbell/core::name') !!}</a> has changed your group memberships.</p>
<p>Login to see your updated permissions.</p>
@stop
