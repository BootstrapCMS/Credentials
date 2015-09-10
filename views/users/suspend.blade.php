<div id="suspend_user" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">{{ trans('credentials.are_you_sure') }}</h4>
            </div>
            <div class="modal-body">
                <p>{{ trans('credentials.you_are_about_to_suspend_this_user') }}</p>
                <p>{{ trans('credentials.are_you_sure_you_wish_to_continue') }}</p>
            </div>
            <div class="modal-footer">
                <a class="btn btn-success" href="{!! URL::route('users.suspend', array('users' => $user->id)) !!}" data-token="{!! Session::getToken() !!}" data-method="POST">{{ trans('credentials.yes') }}</a>
                <button class="btn btn-danger" data-dismiss="modal">{{ trans('credentials.no') }}</a>
            </div>
        </div>
    </div>
</div>
