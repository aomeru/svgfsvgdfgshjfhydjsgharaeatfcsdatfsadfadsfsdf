<div class="card mb-3">
    <div class="card-header bg-dark text-white">
        <h5 class="card-title m-0">Employee Information</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-6 offset-3 col-sm-8 offset-sm-2">
                @include('partials.portal.profile-image',['userdata' => $userdata, 'fluid' => true])
            </div>
        </div>
        <p class="text-center mb-0">
            {{$userdata->fullname}}{!! $userdata->job_title != null ? '<span class="text-muted">, '.$userdata->job_title.'</span>' : '' !!}
            <br>
            <a href="mailto:{{$userdata->email}}" class="text-primary"><i class="fas fa-envelope mr-2"></i>{{$userdata->email}}</a>
            <br>
            <small class="text-muted">{{ $userdata->unit == null ? '' : $userdata->unit->title.', '.$userdata->unit->department->title }}</small>
        </p>
    </div>
</div>
