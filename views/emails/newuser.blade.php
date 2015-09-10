@extends(Config::get('credentials.email'))

@section('content')
<p>{{ trans('credentials.an_admin_from') }} <a href="{!! $url !!}">{!! Config::get('app.name') !!}</a> {{ trans('credentials.has_created_you_an_account') }}</p>
<p>{{ trans('credentials.here_is_your_temporary_password') }}:</p>
<blockquote>{!! $password !!}</blockquote>
<p>{{ trans('credentials.you_should_change_it_to_something_more_memorable') }}</p>
@stop
