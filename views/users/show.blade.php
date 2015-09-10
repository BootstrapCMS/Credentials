@extends(Config::get('credentials.layout'))

@section('title')
<?php $__navtype = 'admin'; ?>
{{ $user->name }}
@stop

@section('top')
<div class="page-header">
<h1>{{ $user->name }}</h1>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-lg-6">
        <p class="lead">
            @if($user->id == Credentials::getUser()->id)
                {{ trans('credentials.currently_showing_your_profile') }}:
            @else
                {{ trans('credentials.currently_showing_user_profile', ['user' => $user->name]) }}:
            @endif
        </p>
    </div>
    <div class="col-lg-6">
        <div class="pull-right visible-lg">
            @auth('admin')
                &nbsp;<a class="btn btn-info" href="{!! URL::route('users.edit', array('users' => $user->id)) !!}"><i class="fa fa-pencil-square-o"></i> {{ trans('credentials.edit_user') }}</a>
            @endauth
            &nbsp;<a class="btn btn-warning" href="#suspend_user" data-toggle="modal" data-target="#suspend_user"><i class="fa fa-ban"></i> {{ trans('credentials.suspend_user') }}</a>
            @auth('admin')
                &nbsp;<a class="btn btn-default" href="#reset_user" data-toggle="modal" data-target="#reset_user"><i class="fa fa-lock"></i> {{ trans('credentials.reset_password') }}</a>
                &nbsp;<a class="btn btn-danger" href="#delete_user" data-toggle="modal" data-target="#delete_user"><i class="fa fa-times"></i> {{ trans('credentials.delete') }}</a>
            @endauth
        </div>
    </div>
    <div class="col-lg-6 hidden-lg">
        @auth('admin')
            &nbsp;<a class="btn btn-info" href="{!! URL::route('users.edit', array('users' => $user->id)) !!}"><i class="fa fa-pencil-square-o"></i> {{ trans('credentials.edit_user') }}</a>
        @endauth
        &nbsp;<a class="btn btn-warning" href="#suspend_user" data-toggle="modal" data-target="#suspend_user"><i class="fa fa-ban"></i> {{ trans('credentials.suspend_user') }}</a>
        @auth('admin')
            &nbsp;<a class="btn btn-default" href="#reset_user" data-toggle="modal" data-target="#reset_user"><i class="fa fa-lock"></i> {{ trans('credentials.reset_password') }}</a>
            &nbsp;<a class="btn btn-danger" href="#delete_user" data-toggle="modal" data-target="#delete_user"><i class="fa fa-times"></i> {{ trans('credentials.delete') }}</a>
        @endauth
    </div>
</div>
<hr>
<h3>User Profile</h3>
<div class="well clearfix">
    <div class="hidden-xs">
        <div class="col-xs-6">
            @if ($user->first_name)
                <p><strong>{{ trans('credentials.first_name') }}:</strong> {!! $user->first_name !!} </p>
            @endif
            @if ($user->last_name)
                <p><strong>{{ trans('credentials.last_name') }}:</strong> {!! $user->last_name !!} </p>
            @endif
            <p><strong>{{ trans('credentials.email') }}:</strong> {!! $user->email !!}</p>
            <p><strong>{{ trans('credentials.groups') }}:</strong> {!! $groups !!}</strong>
        </div>
        <div class="col-xs-6">
            <div class="pull-right">
                <p><em>{{ trans('credentials.account_created') }}: {!! html_ago($user->created_at) !!}</em></p>
                <p><em>{{ trans('credentials.account_updated') }}: {!! html_ago($user->updated_at) !!}</em></p>
                <p><em>{{ trans('credentials.account_activated') }}: {!! $activated !!}</em></p>
                <p><em>{{ trans('credentials.account_suspended') }}: {!! $suspended !!}</em></p>
            </div>
        </div>
    </div>
    <div class="visible-xs">
        <div class="col-xs-12">
            @if ($user->first_name)
                <p><strong>{{ trans('credentials.first_name') }}:</strong> {!! $user->first_name !!} </p>
            @endif
            @if ($user->last_name)
                <p><strong>{{ trans('credentials.last_name') }}:</strong> {!! $user->last_name !!} </p>
            @endif
            <p><strong>{{ trans('credentials.email') }}:</strong> {!! $user->email !!}</p>
            <p><strong>{{ trans('credentials.groups') }}:</strong> {!! $groups !!}</p>
            <p><strong>{{ trans('credentials.account_created') }}:</strong> {!! html_ago($user->created_at) !!}</p>
            <p><strong>{{ trans('credentials.account_updated') }}:</strong> {!! html_ago($user->updated_at) !!}</p>
            <p><strong>{{ trans('credentials.account_activated') }}:</strong> {!! $activated !!}</p>
            <p><strong>{{ trans('credentials.account_suspended') }}:</strong> {!! $suspended !!}</p>
        </div>
    </div>
</div>
<hr>
@include('credentials::users.history')
@stop

@section('bottom')
@include('credentials::users.suspend')
@auth('admin')
    @if (Config::get('credentials.activation'))
        @include('credentials::users.resend')
    @endif
    @include('credentials::users.reset')
    @include('credentials::users.delete')
@endauth
@stop
