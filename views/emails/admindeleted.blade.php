@extends(Config::get('credentials.email'))

@section('content')
<p>{{ trans('credentials.an_admin_from') }} <a href="{!! $url !!}">{!! Config::get('app.name') !!}</a> {{ trans('credentials.has_deleted_your_account_and_all_your_content') }}</p>
<p>{{ trans('credentials.if_this_was_unexpected_please_contact_us_immediately') }}</p>
@stop
