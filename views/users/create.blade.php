@extends(Config::get('core.default'))

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
    $form = ['url' => URL::route('users.store'),
        'method' => 'POST',
        'button' => 'Create New User',
        'defaults' => [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
    ], ];
    foreach ($groups as $group) {
        if ($group->name == 'Users') {
            $form['defaults']['group_'.$group->id] = true;
        } else {
            $form['defaults']['group_'.$group->id] = false;
        }
    }
    ?>
    @include('credentials::users.form')
</div>
@stop

@section('css')
{!! HTML::style('//cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.1.0/css/bootstrap3/bootstrap-switch.min.css') !!}
@stop

@section('js')
{!! HTML::script('//cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.1.0/js/bootstrap-switch.min.js') !!}
<script type="text/javascript">
$(document).ready(function () {
    $(".make-switch").bootstrapSwitch();
});
</script>
@stop
