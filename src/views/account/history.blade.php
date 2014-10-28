@extends(Config::get('views.default', 'layouts.default'))

@section('title')
History
@stop

@section('top')
<div class="page-header">
<h1>History</h1>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <p class="lead">
            Here is your most recent history:
        </p>
    </div>
</div>
<hr>
@include('graham-campbell/credentials::users.history')
@stop

@section('bottom')
@include('graham-campbell/credentials::account.delete')
@stop

@section('css')
{!! HTML::style('//cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/1.9/css/bootstrap3/bootstrap-switch.css') !!}
@stop

@section('js')
{!! HTML::script('//cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/1.9/js/bootstrap-switch.js') !!}
@stop
