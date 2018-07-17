<?php $count = Auth::user()->unreadNotifications->count(); ?>
<div id="notifications" class="is-active">

</div>
{{-- <div id="notif-div"  class="dropdown ml-3 notif d-none d-sm-block">
    <button id="notif-button" class="btn btn-light dropdown-toggle d-flex align-items-center" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" @if($count == 0) disabled @endif>
        <i class="fas fa-bell mr-1 @if($count > 0) fa-2x texxt-danger @endif"></i>@if($count > 0) <span class="badge badge-danger">{{$count}}</span> @endif
    </button>
    @if($count > 0)
    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
        <div class="d-flex justify-content-end pr-2 mb-2">
            <a class="notif-item btn btn-light btn-sm float-right" data-id="markall" data-url="">Mark All As Read</a>
        </div>
        @foreach( Auth::user()->unreadNotifications as $key => $notif)
            <a class="dropdown-item d-flex justify-content-between notif-item" data-id="{{$notif->id}}" data-url="{{$notif->data['url']}}">
                <p class="mb-0">
                    {{$notif->data['title']}}
                </p>
                <span class="text-muted">{{\Carbon\Carbon::parse($notif->created_at)->diffForHumans()}}</span>
            </a>
            @if($key+1 < $count) <div class="dropdown-divider"></div> @endif
        @endforeach
    </div>
    @endif
</div> --}}
