@extends(Config::get('credentials.layout'))

@section('title')
{{ trans('credentials.history') }}
@stop

@section('top')
<div class="page-header">
<h1>{{ trans('credentials.history') }}</h1>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <p class="lead">
            {{ trans('credentials.here_is_your_most_recent_history') }}:
        </p>
    </div>
</div>
<hr>
@include('credentials::users.history')
@stop

@section('bottom')
@include('credentials::account.delete')
@stop
