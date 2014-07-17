<div id="delete_account" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">{{{ Lang::get('graham-campbell/credentials::confirmation.title') }}}</h4>
            </div>
            <div class="modal-body">
                <p>{{{ Lang::get('graham-campbell/credentials::account.confirmdelete') }}}</p>
                <p>{{{ Lang::get('graham-campbell/credentials::confirmation.body') }}}</p>
            </div>
            <div class="modal-footer">
                <a class="btn btn-success" href="{{ URL::route('account.profile.delete') }}" data-token="{{ Session::getToken() }}" data-method="DELETE">Yes</a>
                <button class="btn btn-danger" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>
