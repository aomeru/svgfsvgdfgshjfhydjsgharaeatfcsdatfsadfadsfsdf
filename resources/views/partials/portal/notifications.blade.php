<div id="notifications" class="p-3 px-4">
    <div class="d-flex justify-content-between justify-items-center mb-2">
        <a class="notif-item btn px-0" data-id="markall" data-url="">Mark All As Read</a>
        <button type="button" class="close notif-toggle text-danger" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
    <?php $ncount = Auth::user()->unreadNotifications->count(); ?>
    @if($ncount > 0)
        <div class="list-group list-group-flush">
            @foreach( Auth::user()->unreadNotifications as $key => $notif)
                <a class="d-flex justify-content-between notif-item list-group-item list-group-item-action px-0" data-id="{{$notif->id}}" data-url="{{$notif->data['url']}}">
                    <p class="mb-0 pr-3">
                        {{$notif->data['title']}}
                    </p>
                    <span class="text-muted fontp8x text-right">{{\Carbon\Carbon::parse($notif->created_at)->diffForHumans()}}</span>
                </a>
            @endforeach
        </div>
    @else
        <p class="mb-0 text-muted"><em>You have no notifications.</em></p>
    @endif
</div>
