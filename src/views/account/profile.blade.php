@extends(Config::get('views.default', 'layouts.default'))

@section('title')
Profile
@stop

@section('top')
<div class="page-header">
<h1>Profile</h1>
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
            <a class="btn btn-danger" href="#delete_account" data-toggle="modal" data-target="#delete_account"><i class="fa fa-times"></i> Delete Account</a>
        </div>
    </div>
</div>
<hr>
<h3>Change Details</h3>
<div class="well">
    <?php
    $form = array('url' => URL::route('account.details.patch'),
        'method' => 'PATCH',
        'button' => 'Save Details',
        'defaults' => array(
            'first_name' => Credentials::getUser()->first_name,
            'last_name' => Credentials::getUser()->last_name,
            'email' => Credentials::getUser()->email,
    ));
    ?>
    @include('graham-campbell/credentials::account.details')
</div>
<hr>
<h3>Change Password</h3>
<div class="well">
    <?php
    $form = array('url' => URL::route('account.password.patch'),
        'method' => 'PATCH',
        'button' => 'Save Password',
    );
    ?>
    @include('graham-campbell/credentials::account.password')
</div>
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
