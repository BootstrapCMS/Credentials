@extends(Config::get('views.default', 'layouts.default'))

@section('title')
<?php $__navtype = 'admin'; ?>
Edit {{{ $user->name }}}
@stop

@section('top')
<div class="page-header">
<h1>Edit {{{ $user->name }}}</h1>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-xs-6">
        <p class="lead">
            @if($user->id == Credentials::getUser()->id)
                Currently editing your profile:
            @else
                Currently editing {{ $user->name }}'s profile:
            @endif
        </p>
    </div>
    <div class="col-xs-6">
        <div class="pull-right">
            &nbsp;<a class="btn btn-success" href="{{ URL::route('users.show', array('users' => $user->id)) }}"><i class="fa fa-file-text"></i> Show User</a>
            &nbsp;<a class="btn btn-warning" href="#suspend_user" data-toggle="modal" data-target="#suspend_user"><i class="fa fa-ban"></i> Suspend User</a>
            @auth('admin')
                &nbsp;<a class="btn btn-inverse" href="#reset_user" data-toggle="modal" data-target="#reset_user"><i class="fa fa-lock"></i> Reset Password</a>
                &nbsp;<a class="btn btn-danger" href="#delete_user" data-toggle="modal" data-target="#delete_user"><i class="fa fa-times"></i> Delete</a>
            @endauth
        </div>
    </div>
</div>
<hr>
<div class="well">
    <?php
    $form = array('url' => URL::route('users.update', array('users' => $user->id)),
        'method' => 'PATCH',
        'button' => 'Save User',
        'defaults' => array(
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
    ));
    foreach ($groups as $group) {
        $form['defaults']['group_'.$group->id] = ($user->inGroup($group));
    }
    ?>
    @include('graham-campbell/credentials::users.form')
</div>
@stop

@section('bottom')
@include('graham-campbell/credentials::users.suspend')
@auth('admin')
    @include('graham-campbell/credentials::users.reset')
    @include('graham-campbell/credentials::users.delete')
@endauth
@stop

@section('css')
{{ Asset::styles('form') }}
@stop

@section('js')
{{ Asset::scripts('form') }}
@stop
