@extends(Config::get('credentials.layout'))

@section('title')
{{ trans('credentials.reset_password') }}
@stop

@section('top')
<div class="page-header">
<h1>{{ trans('credentials.reset_password') }}</h1>
</div>
@stop

@section('content')
<p class="lead">{{ trans('credentials.please_enter_your_details') }}:</p>
<div class="well">
    <form class="form-horizontal" action="{{ URL::route('account.reset.post') }}" method="POST">

        {{ csrf_field() }}

        <div class="form-group{!! ($errors->has('email')) ? ' has-error' : '' !!}">
            <label class="col-md-2 col-sm-3 col-xs-10 control-label" for="email">{{ trans('credentials.email_address') }}</label>
            <div class="col-lg-3 col-md-4 col-sm-5 col-xs-10">
                <input name="email" id="email" value="{!! Request::old('email') !!}" type="text" class="form-control" placeholder="{{ trans('credentials.email_address') }}">
                {!! ($errors->has('email') ? $errors->first('email') : '') !!}
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-offset-2 col-sm-offset-3 col-sm-10 col-xs-12">
                <button class="btn btn-primary" type="submit"><i class="fa fa-rocket"></i> {{ trans('credentials.reset_password') }}</button>
            </div>
        </div>

    </form>
</div>
@stop
