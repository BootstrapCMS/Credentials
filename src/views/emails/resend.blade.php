@extends(Config::get('views.email', 'layouts.email'))

@section('content')
<p>{{ Lang::get('graham-campbell/credentials::emails.resend', array('name' => '<a href="{{ $url }}">'.Config::get('platform.name').'</a>')) }}</p>
<p>{{ Lang::get('graham-campbell/credentials::emails.activate') }}, <a href="{{ $link }}">{{ Lang::get('graham-campbell/credentials::emails.click') }}</a>.</p>
@stop
