@extends(Config::get('views.email', 'layouts.email'))

@section('content')
<p>{{ Lang::get('graham-campbell/credentials::emails.newaccount', array('name' => '<a href="{{ $url }}">'.Config::get('platform.name').'</a>')) }}</p>
@if (isset($link))
    <p>{{ Lang::get('graham-campbell/credentials::emails.activate') }}, <a href="{{ $link }}">{{ Lang::get('graham-campbell/credentials::emails.click') }}</a>.</p>
@else
    <p>{{ Lang::get('graham-campbell/credentials::emails.noactivation') }}</p>
@endif
@stop
