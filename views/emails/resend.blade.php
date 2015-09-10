@extends(Config::get('credentials.email'))

@section('content')
<p>{{ trans('credentials.you_have_requested_we_resend_the_activation_link') }} <a href="{!! $url !!}">{!! Config::get('app.name') !!}</a>.</p>
<p>{{ trans('credentials.to_activate_your_account') }} <a href="{!! $link !!}">{{ trans('credentials.click_here') }}</a>.</p>
@stop
