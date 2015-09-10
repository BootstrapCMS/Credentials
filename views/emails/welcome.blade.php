@extends(Config::get('credentials.email'))

@section('content')
<p>{{ trans('credentials.thank_you_for_creating_an_account_on') }} <a href="{!! $url !!}">{!! Config::get('app.name') !!}</a>.</p>
@if (isset($link))
    <p>{{ trans('credentials.to_activate_your_account') }} <a href="{!! $link !!}">{{ trans('credentials.click_here') }}</a>.</p>
@else
    <p>{{ trans('credentials.no_account_activation_is_required') }}</p>
@endif
@stop
