<div class="col-md-6">
    <h3>Security History</h3>
    <hr>
    @if (empty($securityEvents = $user->securityHistory->toArray()))
        <div class="lead">No notable events have occurred yet.</div>
    @else
        @foreach($securityEvents as $event)
            <div class="well clearfix">
                <p><strong>{{{ $event->title }}}</strong> - {{ HTML::ago($event->updated_at) }}</p>
                {{{ $event->description }}}</p>
            </div>
        @endforeach
    @endif
</div>
<hr class="hidden-md hidden-lg">
<div class="col-md-6">
    <h3>Recent Actions</h3>
    <hr>
    @if (empty($actionEvents = $user->actionHistory->toArray()))
        <div class="lead">No notable events have occurred yet.</div>
    @else
        @foreach($actionEvents as $event)
            <div class="well clearfix">
                <p><strong>{{{ $event->title }}}</strong> - {{ HTML::ago($event->updated_at) }}</p>
                {{{ $event->description }}}</p>
            </div>
        @endforeach
    @endif
</div>
