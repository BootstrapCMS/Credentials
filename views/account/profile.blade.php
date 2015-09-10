@extends(Config::get('credentials.layout'))

@section('title')
{{ trans('credentials.profile') }}
@stop

@section('top')
<div class="page-header">
<h1>{{ trans('credentials.profile') }}</h1>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-xs-8">
        <p class="lead">
            Here is your profile:
        </p>
    </div>
    <div class="col-xs-4">
        <div class="pull-right">
            <a class="btn btn-danger" href="#delete_account" data-toggle="modal" data-target="#delete_account"><i class="fa fa-times"></i> {{ trans('credentials.delete_account') }}</a>
        </div>
    </div>
</div>
<hr>
<h3>Change Details</h3>
<div class="well">
    <?php
    $form = ['url' => URL::route('account.details.patch'),
        '_method' => 'PATCH',
        'method' => 'POST',
        'button' => trans('credentials.save_details'),
        'defaults' => [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
    ], ];
    ?>
    @include('credentials::account.details')
</div>
<hr>
<h3>{{ trans('credentials.change_password') }}</h3>
<div class="well">
    <?php
    $form = ['url' => URL::route('account.password.patch'),
        '_method' => 'PATCH',
        'method' => 'POST',
        'button' => trans('credentials.save_password'),
    ];
    ?>
    @include('credentials::account.password')
</div>
@stop

@section('bottom')
@include('credentials::account.delete')
@stop
