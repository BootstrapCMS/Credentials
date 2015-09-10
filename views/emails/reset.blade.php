@extends(Config::get('credentials.email'))

@section('content')
<p>{{ trans('credentials.to_reset_your_password') }} <a href="{!! $link !!}">{{ trans('credentials.click_here') }}.</a></p>
<p>{{ trans('credentials.you_will_receive_your_temporary_password') }}</p>
@stop
