@extends(Config::get('credentials.email'))

@section('content')
<p>{{ trans('credentials.an_admin_from') }} <a href="{!! $url !!}">{!! Config::get('app.name') !!}</a> {{ trans('credentials.has_changed_your_group_memberships') }}</p>
<p>{{ trans('credentials.login_to_see_your_updated_permissions') }}</p>
@stop
