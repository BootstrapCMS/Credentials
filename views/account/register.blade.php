@extends(Config::get('credentials.layout'))

@section('title')
{{ trans('credentials.register') }}
@stop

@section('top')
<div class="page-header">
<h1>{{ trans('credentials.register') }}</h1>
</div>
@stop

@section('content')
<p class="lead">{{ trans('credentials.please_enter_your_details') }}:</p>
<div class="well">
    <form class="form-horizontal" action="{{ URL::route('account.register.post') }}" method="POST">

        {{ csrf_field() }}

        <div class="form-group{!! ($errors->has('first_name')) ? ' has-error' : '' !!}">
            <label class="col-md-2 col-sm-3 col-xs-10 control-label" for="first_name">{{ trans('credentials.first_name') }}</label>
            <div class="col-lg-3 col-md-4 col-sm-5 col-xs-10">
                <input name="first_name" id="first_name" value="{!! Request::old('first_name') !!}" type="text" class="form-control" placeholder="{{ trans('credentials.first_name') }}">
                {!! ($errors->has('first_name') ? $errors->first('first_name') : '') !!}
            </div>
        </div>

        <div class="form-group{!! ($errors->has('last_name')) ? ' has-error' : '' !!}">
            <label class="col-md-2 col-sm-3 col-xs-10 control-label" for="last_name">{{ trans('credentials.last_name') }}</label>
            <div class="col-lg-3 col-md-4 col-sm-5 col-xs-10">
                <input name="last_name" id="last_name" value="{!! Request::old('last_name') !!}" type="text" class="form-control" placeholder="{{ trans('credentials.last_name') }}">
                {!! ($errors->has('last_name') ? $errors->first('last_name') : '') !!}
            </div>
        </div>

        <div class="form-group{!! ($errors->has('email')) ? ' has-error' : '' !!}">
            <label class="col-md-2 col-sm-3 col-xs-10 control-label" for="email">{{ trans('credentials.email') }}</label>
            <div class="col-lg-3 col-md-4 col-sm-5 col-xs-10">
                <input name="email" id="email" value="{!! Request::old('email') !!}" type="text" class="form-control" placeholder="{{ trans('credentials.email') }}">
                {!! ($errors->has('email') ? $errors->first('email') : '') !!}
            </div>
        </div>

        <div class="form-group{!! $errors->has('password') ? ' has-error' : '' !!}">
            <label class="col-md-2 col-sm-3 col-xs-10 control-label" for="password">{{ trans('credentials.password') }}</label>
            <div class="col-lg-3 col-md-4 col-sm-5 col-xs-10">
                <input name="password" id="password" value="" type="password" class="form-control" placeholder="{{ trans('credentials.password') }}">
                {!! ($errors->has('password') ?  $errors->first('password') : '') !!}
            </div>
        </div>

        <div class="form-group{!! $errors->has('password_confirmation') ? ' has-error' : '' !!}">
            <label class="col-md-2 col-sm-3 col-xs-10 control-label" for="password_confirmation">{{ trans('credentials.confirm_password') }}</label>
            <div class="col-lg-3 col-md-4 col-sm-5 col-xs-10">
                <input name="password_confirmation" id="password_confirmation" value="" type="password" class="form-control" placeholder="{{ trans('credentials.confirm_password') }}">
                {!! ($errors->has('password_confirmation') ? $errors->first('password_confirmation') : '') !!}
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-offset-2 col-sm-offset-3 col-sm-10 col-xs-12">
                <button class="btn btn-primary" type="submit"><i class="fa fa-rocket"></i> {{ trans('credentials.register') }}</button>
                <button class="btn btn-default" type="reset">{{ trans('credentials.reset') }}</button>
            </div>
        </div>

    </form>
</div>
@stop
