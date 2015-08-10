<div class="col-md-6">
    <h3>Security History</h3>
    <hr>
    @forelse($user->securityHistory->toArray() as $event)
        <div class="well clearfix">
            <p><strong>{{ $event->title }}</strong> - {!! html_ago($event->updated_at) !!}</p>
            <p>{{ $event->description }}</p>
        </div>
    @empty
        <div class="lead">No notable events have occurred yet.</div>
    @endforelse
</div>
<hr class="hidden-md hidden-lg">
<div class="col-md-6">
    <h3>Recent Actions</h3>
    <hr>
    @forelse($user->actionHistory->toArray() as $event)
        <div class="well clearfix">
            <p><strong>{{ $event->title }}</strong> - {!! html_ago($event->updated_at) !!}</p>
            <p>{{ $event->description }}</p>
        </div>
    @empty
        <div class="lead">No notable events have occurred yet.</div>
    @endforelse
</div>
