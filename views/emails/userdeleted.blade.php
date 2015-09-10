@extends(Config::get('credentials.email'))

@section('content')
<p>{{ trans('credentials.you_have_deleted_your_account') }} <a href="{!! $url !!}">{!! Config::get('app.name') !!}</a>.</p>
<p>{{ trans('credentials.if_this_was_not_you_please_contact_us_immediately') }}</p>
@stop
