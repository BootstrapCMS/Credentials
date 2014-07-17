@extends(Config::get('views.email', 'layouts.email'))

@section('content')
<p>{{ Lang::get('graham-campbell/credentials::emails.temporarypassword') }}</p>
<blockquote>{{{ $password }}}</blockquote>
<p>{{ Lang::get('graham-campbell/credentials::emails.changepassword') }}</p>
@stop
