@extends(Config::get('views.default', 'layouts.default'))

@section('title')
<?php $__navtype = 'admin'; ?>
Create User
@stop

@section('top')
<div class="page-header">
<h1>Create User</h1>
</div>
@stop

@section('content')
<div class="well">
    <?php
    $form = array('url' => URL::route('users.store'),
        'method' => 'POST',
        'button' => 'Create New User',
        'defaults' => array(
            'first_name' => '',
            'last_name' => '',
            'email' => '',
    ), );
    foreach ($groups as $group) {
        if ($group->name == 'Users') {
            $form['defaults']['group_'.$group->id] = true;
        } else {
            $form['defaults']['group_'.$group->id] = false;
        }
    }
    ?>
    @include('graham-campbell/credentials::users.form')
</div>
@stop

@section('css')
{!! HTML::style('//cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/1.9/css/bootstrap3/bootstrap-switch.css') !!}
@stop

@section('js')
{!! HTML::script('//cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/1.9/js/bootstrap-switch.js') !!}
@stop
