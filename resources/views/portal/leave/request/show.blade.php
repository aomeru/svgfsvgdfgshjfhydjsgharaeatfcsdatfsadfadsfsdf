<?php
$color = [
    'pending' => 'muted',
    'submitted' => 'primary',
    'manager_approved' => 'success',
    'manager_declined' => 'danger',
    'hr_approved' => 'success',
    'hr_declined' => 'danger',
    'completed' => 'success',
];
?>

@extends('layouts.portal')
@section('page_title','Leave Request: '.$item->code.' - ')
@section('portal_page_title') <i class="fas fa-user-tie mr-3"></i>Leave Request: {{$item->code}} @endSection

@section('bc')
    <nav aria-label="breadcrumb" class="d-none d-md-block">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('portal')}}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{route('portal.users')}}">Leave</a></li>
            <li class="breadcrumb-item"><a href="{{route('portal.leave.request')}}">Requests</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $item->code }}</li>
        </ol>
    </nav>
@endSection

@section('content')

<div class="row">
    <div class="col-sm-3">
        @include('partials.portal.profile',['userdata' => $item->leave->user])
        <div class="card">
            <div class="card-body">
                @if(Laratrust::can('update-leave-allocation'))
                    <a href="{{ route('leave-allocation.show', Crypt::encrypt($item->leave->user->id)) }}" class="btn btn-info btn-sm text-white" title="View {{ $item->leave->user->fullname }} leave allocations"><i class="fas fa-eye"></i> View Allocations</a>
                @endif
            </div>
        </div>
    </div>

    <div class="col-sm-9">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="card-title mb-0">Application</h5>
            </div>
            <div class="card-body">
                <p class="text-right">
                    Application Status: <em class="text-{{$color[$item->status]}}">{{$item->status}}</em>
                </p>
                <hr class="my-3">
                <div class="row">
                    <div class="col-6 col-sm-4">
                        <p><span class="text-secondary">Leave Type: </span>{{$item->leave->leave_type->title }}</p>
                    </div>
                    <div class="col-6 col-sm-4">
                        <p><span class="text-secondary">Start date: </span>{{ date('jS M, Y', strtotime($item->leave->start_date)) }}</p>
                    </div>

                    <div class="col-6 col-sm-4">
                        <p><span class="text-secondary">End date: </span>{{ date('jS M, Y', strtotime($item->leave->end_date)) }}</p>
                    </div>
                    <div class="col-6 col-sm-4">
                        <p><span class="text-secondary">Date of Resumption: </span>{{ date('jS M, Y', strtotime($item->leave->back_on)) }}</p>
                    </div>

                    <div class="col-6 col-sm-4">
                        <p><span class="text-secondary">Manager: </span>{{$item->manager->fullname}}</p>
                    </div>
                    <div class="col-6 col-sm-4">
                        <p><span class="text-secondary">Relieving Staff: </span>{{$item->leave->ruser->fullname}}</p>
                    </div>
                </div>
            </div>
        </div>



        <div class="card mt-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="card">
                            <div class="card-header bg-dark text-white">
                                <h5 class="card-title mb-0">Employee Leave Record</h5>
                            </div>
                            <div class="card-body">
                                @if($item->manager->id == Auth::user()->id)

                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        @if($item->log != null)

                            <table class="table data-table" width="100%" data-page-length="10" data-order="2">

                                <thead>
                                    <th class="text-center"></th>
                                    <th>Comment</th>
                                    <th class="text-right">Date</th>
                                </thead>

                                <tbody>

                                    @foreach($item->log()->orderby('created_at','desc')->get() as $x)

                                        <tr>

                                            <td class="text-center">
                                                <?php
                                                $img = $x->type == 'sys' ? asset('images/brand-logo.png') : $x->user->photo;
                                                if($img == '') $img = asset('images/user.png');
                                                ?>
                                                <img src="{{$img}}" class="rounded-circle border border-light" alt="" width="50px" height="50px">
                                            </td>

                                            <td><em class="text-muted">{{ $x->type == 'sys' ? 'Automated Message' : $x->user->fullname }}</em> <br> {{$x->comment}}</td>

                                            <td class="text-right text-muted">{{\Carbon\Carbon::parse($x->created_at)->diffForHumans()}}</td>

                                        </tr>

                                    @endforeach

                                    <tr>

                                        <td class="text-center">
                                            <img src="{{$item->leave->user->photo == null ? asset('images/user.png') : 'data:image.jpg;base64,'.$item->leave->user->photo}}" class="rounded-circle border border-light" alt="" width="50px" height="50px">
                                        </td>

                                        <td><em class="text-muted">{{ $item->leave->user->fullname }}</em> <br> {{$item->leave->comment}}</td>

                                        <td class="text-right text-muted">{{\Carbon\Carbon::parse($item->created_at)->diffForHumans()}}</td>

                                    </tr>

                                </tbody>

                            </table>

                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection






@section('page_footer')

@if(Laratrust::can('update-leave-request'))
<div class="modal fade" id="edit-modal" tabinndex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog w500" role="document">
		<div class="modal-content">
			<form method="post">

				<div class="modal-header">
                    <h5 class="modal-title">Update </h5>
				</div>

				<div class="modal-body">

				</div>

				<div class="modal-footer">
                    <button type="button" class="btn-default btn" data-dismiss="modal" aria-label="Close"><i class="fas fa-times mr-2"></i>Cancel</button>
                    <button class="btn-success btn" id='edit-btn' type="submit" role="button"><i class="fas fa-check mr-2"></i>Update</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endif

@endsection







@section('scripts')

<script>

    $(document).ready(function() {
        $('.data-table').DataTable({
            "order": []
        });
        $('.select').select2();

        // $('#edit-modal').modal('show');

        $(document).on('click', '#edit-btn', function(e){

			e.preventDefault();

            var btn = $(this),
				btn_text = btn.html(),
				users = $("#users").val(),
                user_unit = $("#user-unit").is(':checked'),
				token ='{{ Session::token() }}',
				url = "{{route('managers.update', ':id')}}";
                url = url.replace(':id',"{{Crypt::encrypt($item->id)}}");

			$.ajax({
				type: "PUT",
				url: url,
				data: {
					user_unit: user_unit,
					users: users,
					_token: token
				},
				beforeSend: function () {
					btn.html('<i class="fas fa-spinner fa-spin"></i>');
				},
				success: function(response) {
					btn.html(btn_text);
                    $('#edit-modal').modal('hide');
					swal_alert('Manager subordinates updated','','success','Continue');
                    window.setTimeout(function(){
                        window.location.href = "{{route('managers.show',Crypt::encrypt($item->id))}}";
                    },1000);
				},
				error: function(jqXHR, exception){
					btn.html(btn_text);
					var error = getErrorMessage(jqXHR, exception);
                    swal_alert('Failed to update Manager subordinates',error,'error','Go Back');
				}
			});
        });

    });

</script>

@endsection
