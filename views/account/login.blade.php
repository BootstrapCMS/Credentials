@extends(Config::get('credentials.layout'))

@section('title')
{{ trans('credentials.login') }}
@stop

@section('top')
<div class="page-header">
<h1>{{ trans('credentials.login') }}</h1>
</div>
@stop

@section('content')
<p class="lead">{{ trans('credentials.please_enter_your_details') }}:</p>
<div class="well">
    <form class="form-horizontal" action="{{ URL::route('account.login.post') }}" method="POST">

        {{ csrf_field() }}

        <div class="form-group{!! ($errors->has('email')) ? ' has-error' : '' !!}">
            <label class="col-md-2 col-sm-3 col-xs-10 control-label" for="email">{{ trans('credentials.email') }}</label>
            <div class="col-lg-3 col-md-4 col-sm-5 col-xs-10">
                <input name="email" id="email" value="{!! Request::old('email') !!}" type="text" class="form-control" placeholder="{{ trans('credentials.email') }}">
                {!! ($errors->has('email') ? $errors->first('email') : '') !!}
            </div>
        </div>

       <div class="form-group{!! ($errors->has('password')) ? ' has-error' : '' !!}">
            <label class="col-md-2 col-sm-3 col-xs-10 control-label" for="password">{{ trans('credentials.password') }}</label>
            <div class="col-lg-3 col-md-4 col-sm-5 col-xs-10">
                <input name="password" id="password" value="" type="password" class="form-control" placeholder="{{ trans('credentials.password') }}">
                {!! ($errors->has('password') ? $errors->first('password') : '') !!}
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-offset-2 col-sm-offset-3 col-sm-10 col-xs-12">
                <div class="checkbox">
                    <label><input type="checkbox" name="rememberMe" value="1"> {{ trans('credentials.remember_me') }}</label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-offset-2 col-sm-offset-3 col-sm-10 col-xs-12">
                <button class="btn btn-primary" type="submit"><i class="fa fa-rocket"></i> {{ trans('credentials.log_in') }}</button>
                @if (Config::get('credentials.activation'))
                    <label><a href="{!! URL::route('account.reset') !!}" class="btn btn-link">{{ trans('credentials.forgot_password') }}</a>/<a href="{!! URL::route('account.resend') !!}" class="btn btn-link">{{ trans('credentials.not_activated') }}</a></label>
                @else
                    <label><a href="{!! URL::route('account.reset') !!}" class="btn btn-link">{{ trans('credentials.forgot_password') }}</a>
                @endif
            </div>
        </div>

  </form>
</div>
@stop
