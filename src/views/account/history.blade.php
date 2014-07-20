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
{{ Asset::styles('form') }}
@stop

@section('js')
{{ Asset::scripts('form') }}
@stop
