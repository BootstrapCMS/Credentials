<form class="form-horizontal" action="{{ $form['url'] }}" method="{{ $form['method'] }}">

    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ $form['_method'] }}">

    <div class="form-group{!! ($errors->has('first_name')) ? ' has-error' : '' !!}">
        <label class="col-md-2 col-sm-3 col-xs-10 control-label" for="first_name">{{ trans('credentials.first_name') }}</label>
        <div class="col-lg-3 col-md-4 col-sm-5 col-xs-10">
            <input name="first_name" id="first_name" value="{!! Request::old('first_name', $form['defaults']['first_name']) !!}" type="text" class="form-control" placeholder="{{ trans('credentials.first_name') }}">
            {!! ($errors->has('first_name') ? $errors->first('first_name') : '') !!}
        </div>
    </div>

    <div class="form-group{!! ($errors->has('last_name')) ? ' has-error' : '' !!}">
        <label class="col-md-2 col-sm-3 col-xs-10 control-label" for="last_name">{{ trans('credentials.last_name') }}</label>
        <div class="col-lg-3 col-md-4 col-sm-5 col-xs-10">
            <input name="last_name" id="last_name" value="{!! Request::old('last_name', $form['defaults']['last_name']) !!}" type="text" class="form-control" placeholder="{{ trans('credentials.last_name') }}">
            {!! ($errors->has('last_name') ? $errors->first('last_name') : '') !!}
        </div>
    </div>

    <div class="form-group{!! ($errors->has(' email')) ? ' has-error' : '' !!}">
        <label class="col-md-2 col-sm-3 col-xs-10 control-label" for="email">{{ trans('credentials.email') }}</label>
        <div class="col-lg-3 col-md-4 col-sm-5 col-xs-10">
            <input name="email" id="email" value="{!! Request::old('email', $form['defaults']['email']) !!}" type="text" class="form-control" placeholder="{{ trans('credentials.email') }}">
            {!! ($errors->has('email') ? $errors->first('email') : '') !!}
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-offset-2 col-sm-offset-3 col-sm-10 col-xs-12">
            <button class="btn btn-primary" type="submit"><i class="fa fa-rocket"></i> {!! $form['button'] !!}</button>
        </div>
    </div>

</form>
