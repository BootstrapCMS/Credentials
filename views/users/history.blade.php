<div class="col-md-6">
    <h3>{{ trans('credentials.security_history') }}</h3>
    <hr>
    @forelse($user->securityHistory->toArray() as $event)
        <div class="well clearfix">
            <p><strong>{{ $event->title }}</strong> - {!! html_ago($event->updated_at) !!}</p>
            <p>{{ $event->description }}</p>
        </div>
    @empty
        <div class="lead">{{ trans('credentials.no_notable_events_have_occurred_yet') }}</div>
    @endforelse
</div>
<hr class="hidden-md hidden-lg">
<div class="col-md-6">
    <h3>{{ trans('credentials.recent_actions') }}</h3>
    <hr>
    @forelse($user->actionHistory->toArray() as $event)
        <div class="well clearfix">
            <p><strong>{{ $event->title }}</strong> - {!! html_ago($event->updated_at) !!}</p>
            <p>{{ $event->description }}</p>
        </div>
    @empty
        <div class="lead">{{ trans('credentials.no_notable_events_have_occurred_yet') }}</div>
    @endforelse
</div>
