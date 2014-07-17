@extends(Config::get('views.email', 'layouts.email'))

@section('content')
<p>{{ Lang::get('graham-campbell/credentials::emails.created', array('name' => '<a href="{{ $url }}">'.Config::get('platform.name').'</a>')) }}</p>
<p>{{ Lang::get('graham-campbell/credentials::emails.temporarypassword') }}</p>
<blockquote>{{{ $password }}}</blockquote>
<p>{{ Lang::get('graham-campbell/credentials::emails.changepassword') }}</p>
@stop
