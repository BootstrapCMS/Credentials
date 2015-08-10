@extends(Config::get('credentials.layout'))

@section('title')
Reset Password
@stop

@section('top')
<div class="page-header">
<h1>Reset Password</h1>
</div>
@stop

@section('content')
<p class="lead">Please enter your details:</p>
<div class="well">
    <form class="form-horizontal" action="{{ URL::route('account.reset.post') }}" method="POST">

        {{ csrf_field() }}

        <div class="form-group{!! ($errors->has('email')) ? ' has-error' : '' !!}">
            <label class="col-md-2 col-sm-3 col-xs-10 control-label" for="email">Email Address</label>
            <div class="col-lg-3 col-md-4 col-sm-5 col-xs-10">
                <input name="email" id="email" value="{!! Request::old('email') !!}" type="text" class="form-control" placeholder="Email Address">
                {!! ($errors->has('email') ? $errors->first('email') : '') !!}
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-offset-2 col-sm-offset-3 col-sm-10 col-xs-12">
                <button class="btn btn-primary" type="submit"><i class="fa fa-rocket"></i> Reset Password</button>
            </div>
        </div>

    </form>
</div>
@stop
