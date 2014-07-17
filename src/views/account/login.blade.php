@extends(Config::get('views.default', 'layouts.default'))

@section('title')
{{{ Lang::get('graham-campbell/credentials::account.login.title') }}}
@stop

@section('top')
<div class="page-header">
<h1>{{{ Lang::get('graham-campbell/credentials::account.login.title') }}}</h1>
</div>
@stop

@section('content')
<p class="lead">{{{ Lang::get('graham-campbell/credentials::user.details') }}}:</p>
<div class="well">
    {{ Form::open(array('url' => URL::route('account.login.post'), 'method' => 'POST', 'class' => 'form-horizontal')) }}

        <div class="form-group{{ ($errors->has('email')) ? ' has-error' : '' }}">
            <label class="col-md-2 col-sm-3 col-xs-10 control-label" for="email">{{{ Lang::get('graham-campbell/credentials::user.email') }}}</label>
            <div class="col-lg-3 col-md-4 col-sm-5 col-xs-10">
                <input name="email" id="email" value="{{ Request::old('email') }}" type="text" class="form-control" placeholder="{{{ Lang::get('graham-campbell/credentials::user.email') }}}">
                {{ ($errors->has('email') ? $errors->first('email') : '') }}
            </div>
        </div>

       <div class="form-group{{ ($errors->has('password')) ? ' has-error' : '' }}">
            <label class="col-md-2 col-sm-3 col-xs-10 control-label" for="password">{{{ Lang::get('graham-campbell/credentials::user.password') }}}</label>
            <div class="col-lg-3 col-md-4 col-sm-5 col-xs-10">
                <input name="password" id="password" value="" type="password" class="form-control" placeholder="{{{ Lang::get('graham-campbell/credentials::user.password') }}}">
                {{ ($errors->has('password') ? $errors->first('password') : '') }}
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-offset-2 col-sm-offset-3 col-sm-10 col-xs-12">
                <div class="checkbox">
                    <label><input type="checkbox" name="rememberMe" value="1"> {{{ Lang::get('graham-campbell/credentials::account.login.remember') }}}</label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-offset-2 col-sm-offset-3 col-sm-10 col-xs-12">
                <button class="btn btn-primary" type="submit"><i class="fa fa-rocket"></i> {{{ Lang::get('graham-campbell/credentials::account.login.submit') }}}</button>
                @if (Config::get('graham-campbell/credentials::activation'))
                    <label><a href="{{ URL::route('account.reset') }}" class="btn btn-link">{{{ Lang::get('graham-campbell/credentials::account.login.password') }}}?</a>/<a href="{{ URL::route('account.resend') }}" class="btn btn-link">{{{ Lang::get('graham-campbell/credentials::account.login.activated') }}}?</a></label>
                @else
                    <label><a href="{{ URL::route('account.reset') }}" class="btn btn-link">{{{ Lang::get('graham-campbell/credentials::account.login.password') }}}?</a>
                @endif
            </div>
        </div>

  {{ Form::close() }}
</div>
@stop

@section('css')
{{ Asset::styles('form') }}
@stop

@section('js')
{{ Asset::scripts('form') }}
@stop
