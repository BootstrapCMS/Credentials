@extends(Config::get('views.email', 'layouts.email'))

@section('content')
<p>{{ Lang::get('graham-campbell/credentials::emails.reset') }}, <a href="{{ $link }}">{{ Lang::get('graham-campbell/credentials::emails.click') }}</a>.</p>
<p>{{ Lang::get('graham-campbell/credentials::emails.confirm') }}</p>
@stop
