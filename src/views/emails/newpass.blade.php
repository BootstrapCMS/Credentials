@extends(Config::get('views.email', 'layouts.email'))

@section('content')
<p>{{ Lang::get('graham-campbell/credentials::emails.newpass', array('name' => '<a href="{{ $url }}">'.Config::get('platform.name').'</a>')) }}</p>
<p>{{ Lang::get('graham-campbell/credentials::emails.contact') }}</p>
@stop
