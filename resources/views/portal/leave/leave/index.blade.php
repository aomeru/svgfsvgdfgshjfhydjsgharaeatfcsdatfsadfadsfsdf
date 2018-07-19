<?php
$color = [
    'pending' => 'muted',
    'submitted' => 'primary',
    'manager_approved' => 'success',
    'manager_deferred' => 'success',
    'manager_declined' => 'danger',
    'hr_approved' => 'success',
    'hr_deferred' => 'success',
    'hr_declined' => 'danger',
    'completed' => 'success',
];
$edit_allow = ['submitted','manager_declined'];
$cancel_allow = ['submitted','manager_approved','manager_declined','manager_deferred'];
?>

@extends('layouts.portal')
@section('page_title','Leave - ')
@section('portal_page_title') <i class="fas fa-calendar mr-3"></i>Leave @endSection

@section('bc')
    <nav aria-label="breadcrumb" class="d-none d-md-block">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('portal')}}">Dashboard</a></li>
            <li class="breadcrumb-item">Leave</li>
            <li class="breadcrumb-item active" aria-current="page">My Leave</li>
        </ol>
    </nav>
@endSection

@section('content')

<div class="row">
    <div class="col-sm-9 mb-3 mb-sm-0">
        <div class="card bg-white mb-3">
            <div class="card-body sm-font125x d-block d-sm-flex justify-content-between">
                <p class="mb-0"><span class="c-999">Total Applications:</span> {{ $alist->count() }}</p>
                <span class="c-ccc d-none d-sm-block">|</span>
                <p class="mb-0"><span class="c-999">Completed Leave:</span> {{ $calist->count() }}</p>
                <span class="c-ccc d-none d-sm-block">|</span>
                <p class="mb-0"><span class="c-999">Days Out:</span> <span class="text-primary">0</span></p>
                <span class="c-ccc d-none d-sm-block">|</span>
                <p class="mb-0"><span class="c-999">Status:</span> @if($on_leave) <span class="text-danger">On Leave</span> @else <span class="text-success">On Duty</span> @endif</p>
                <span class="c-ccc d-none d-sm-block">|</span>
                <p class="mb-0"><span class="c-999">Leave Year:</span> <span class="text-primary">{{config('app.leave_year')}}</span></p>
            </div>
        </div>

        <div class="card mb-3 mb-sm-5">
            <div class="card-header bg-dark text-white">
                <h5 class="card-title m-0">Leave</h5>
            </div>
            <div class="card-body">
                @if($clist->count() == 0)
                    <p class="alert alert-secondary mb-0">
                        You have no created leave record
                    </p>
                @else
                    <div class="table-responsive">

                        <table class="table table-striped table-bordered table-hover nowrap data-table" width="100%" data-page-length="25">

                            <thead>
                                <tr class="active">
                                    <th>#</th>
                                    <th>Leave</th>
                                    <th>Type</th>
                                    <th class="text-center">Start Date</th>
                                    <th class="text-center">End Date</th>
                                    <th class="text-center">Return Date</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Last Modified</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>

                            <tbody>

                                @php $row_count = 1 @endphp

                                @foreach($clist as $item)

                                    <tr>

                                        <td>{{ $row_count }}</td>

                                        <td class="text-uppercase">
                                            <a href="{{ route('portal.leave.edit', Crypt::encrypt($item->id)) }}" class="text-underline" title="Edit {{ Auth::user()->fullname.'-'.strtotime($item->created_at) }} leave">
                                                {{ $item->leave_request == null ? Auth::user()->username.'-'.strtotime($item->created_at) : $item->leave_request->code }}
                                            </a>
                                        </td>

                                        <td>{{ $item->leave_type->title }}</td>

                                        <td class="text-center">{{ date('jS M, Y', strtotime($item->start_date)) }}</td>

                                        <td class="text-center">{!! $item->end_date == null ? '<em class="text-muted">N/A</em>' : date('jS M, Y', strtotime($item->end_date)) !!}</td>

                                        <td class="text-center">{!! $item->back_on == null ? '<em class="text-muted">N/A</em>' : date('jS M, Y', strtotime($item->back_on)) !!}</td>

                                        <td class="text-center text-{{$color[$item->status]}}">{{ $item->status }}</td>

                                        <td class="text-center">{{\Carbon\Carbon::parse($item->updated_at)->diffForHumans()}}</td>

                                        <td class="text-right">
                                            @if(Laratrust::can('update-leave'))
                                                <a href="{{ route('portal.leave.edit', Crypt::encrypt($item->id)) }}" class="btn btn-primary btn-sm text-white" title="Edit {{ Auth::user()->fullname.'-'.strtotime($item->created_at) }} leave"><i class="fas fa-pencil-alt"></i></a>
                                            @endif

                                            @if(Laratrust::can('delete-leave') && $item->status == 'pending')
                                                <a href="{{ route('portal.leave.delete', Crypt::encrypt($item->id)) }}" class="btn btn-danger btn-sm text-white" title="Delete {{ Auth::user()->fullname.'-'.strtotime($item->created_at) }} leave"><i class="far fa-trash-alt"></i></a>
                                            @endif
                                        </td>

                                    </tr>

                                    @php $row_count++ @endphp

                                @endforeach

                            </tbody>

                        </table>

                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="card-title m-0">Leave Applications</h5>
            </div>
            <div class="card-body">
                @if($alist->count() == 0)
                    <p class="alert alert-secondary mb-0">
                        You have no existing leave application
                    </p>
                @else
                    <div class="table-responsive">

                        <table class="table table-striped table-bordered table-hover nowrap data-table" width="100%" data-page-length="25">

                            <thead>
                                <tr class="active">
                                    <th>#</th>
                                    <th>Leave</th>
                                    <th>Type</th>
                                    <th class="text-center">Start Date</th>
                                    <th class="text-center">End Date</th>
                                    <th class="text-center">Return Date</th>
                                    <th>Relieve Staff</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Last Modified</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>

                            <tbody>

                                @php $row_count = 1 @endphp

                                @foreach($alist as $item)

                                    <tr>

                                        <td>{{ $row_count }}</td>

                                        <td>
                                            <a href="{{ route('portal.leave.request.show', $item->leave_request->code) }}" class="text-underline" title="View {{ Auth::user()->fullname.'-'.strtotime($item->created_at) }} leave request">
                                                {{ $item->leave_request->code }}
                                            </a>
                                        </td>

                                        <td>{{ $item->leave_type->title }}</td>

                                        <td class="text-center">{{ date('jS M, Y', strtotime($item->start_date)) }}</td>

                                        <td class="text-center">{!! $item->end_date == null ? '<em class="text-muted">N/A</em>' : date('jS M, Y', strtotime($item->end_date)) !!}</td>

                                        <td class="text-center">{!! $item->back_on == null ? '<em class="text-muted">N/A</em>' : date('jS M, Y', strtotime($item->back_on)) !!}</td>

                                        <td>{!! $item->ruser == null ? '<em class="text-muted">N/A</em>' : $item->ruser->fullname !!}</td>

                                        <td class="text-center text-{{$color[$item->leave_request->status]}}">{{ $item->leave_request->status }}</td>

                                        <td class="text-center">{{\Carbon\Carbon::parse($item->leave_request->updated_at)->diffForHumans()}}</td>

                                        <td class="text-right">
                                            @if(in_array($item->status, $cancel_allow) && Laratrust::can('update-leave-request'))
                                            <a href="{{ Crypt::encrypt($item->id) }}" class="btn btn-warning btn-sm text-white" title="Cancel leave request"><i class="fas fa-ban"></i></a>
                                            @endif
                                        </td>

                                    </tr>

                                    @php $row_count++ @endphp

                                @endforeach

                            </tbody>

                        </table>

                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-sm-3">

        <div class="mb-3">
            @if(Laratrust::can('create-leave'))
                <a href="{{ route('portal.leave.apply') }}" class="btn btn-primary btn-sm" title="Create new leave application"><i class="fas fa-calendar-plus mr-2"></i>Apply</a>
            @endif
        </div>

        <div class="alert aler-dark bg-dark text-white mb-2">
            <h6 class="alert-heading m-0">Leave Allocations</h6>
        </div>
        @if($las->count() == 0)
            <div class="alert alert-primary" role="alert">You don't have a any allocations yet</div>
        @endif
        @foreach($las as $key => $la)
            <div class="card card-custom shadomw-sm mb-3">
                <?php
                $color = 'info';
                $marker = round($la->leave_type->allowed/3);
                if($la->allowed <= $marker) $color = 'danger'; elseif($la->allowed <= ($marker* 2)) $color = 'warning';
                ?>
                <div class="card-header text-white progress-bar-striped bg-{{$color}}">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title ttext-capitalize m-0">{{$la->leave_type->title}}</h5>
                        <div class="display-4">{{$la->allowed}}</div>
                    </div>
                </div>
                <div class="card-body text-whitee bg-darkk p-3">
                    <p class="mb-0">
                        <span class="text-secondary">Applications: </span>
                        <span>
                            <?php
                            $acount = Auth::user()->leave()->where('leave_type_id',$la->leave_type->id)->where('status','<>','pending')->count();
                            ?>
                            {{$acount}}
                        </span>
                    </p>
                    <p class="mb-0">
                        <span class="text-secondary">Taken: </span>
                        <span>
                            <?php
                            $tcount = Auth::user()->leave()->where('leave_type_id',$la->leave_type->id)->whereIn('status', ['called off','completed'])->count();
                            ?>
                            {{$tcount}}
                        </span>
                    </p>
                    <p class="mb-0">
                        <span class="text-secondary">Cancelled: </span>
                        <span>
                            <?php
                            $ccount = Auth::user()->leave()->where('leave_type_id',$la->leave_type->id)->whereIn('status', ['cancelled'])->count();
                            ?>
                            {{$ccount}}
                        </span>
                    </p>
                </div>
            </div>
        @endforeach
    </div>
</div>

@endsection






@section('page_footer')

@if(Laratrust::can('create-leave'))
{{-- <div class="modal fade" id="add-modal" tabinndex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog sm-w500" role="document">
		<div class="modal-content">
            <form method="post">

				<div class="modal-header"><h5 class="modal-title">Create Leave Application</h5></div>

				<div class="modal-body">

                    <div class="form-group">
                        <label for="ltype" class="form-control-label sr-onlyy">Leave Type</label>
                        <select name="ltype" id="ltype" class="form-control select" style="width: 100%;">
                            <option>Select Leave Type</option>
                            @foreach($las as $key => $la)
                                <option value="{{ $la->leave_type->title }}" data-allowed="{{ $la->allowed }}">{{ $la->leave_type->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="start-date" class="form-control-label">Start Date</label>
                                <div class="input-group mb-3">
                                    <input id="start-date" type="date" class="form-control" placeholder="Start Date">
                                    <label for="start-date" class="input-group-append mb-0">
                                        <span class="input-group-text"><span class="fas fa-calendar"></span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div id="days-wrap" class="form-group"></div>
                        </div>
                    </div>

                </div>

				<div class="modal-footer">
                    <input id="permmode" type="hidden" readonly value="basic">
                    <button type="button" class="btn-default btn" data-dismiss="modal" aria-label="Close"><i class="fas fa-times mr-2"></i>Cancel</button>
                    <button class="btn-primary btn" id='add-btn' type="submit" role="button"><i class="fas fa-check mr-2"></i>Apply</button>
				</div>
			</form>
		</div>
	</div>
</div> --}}
@endif

@endsection







@section('scripts')

<script>

    // function switchfunc(elem)
    // {
    //     var y = elem.find('option:selected').data('allowed'),
    //             elem = '<label for="no-days" class="form-control-label sr-onlyy">Select Number of Days</label><select id="no-days" class="form-control select" style="width: 100%;">';
    //     for(x = 1; x <= y; x++)
    //     {
    //         elem = elem + '<option value="'+x+'">'+x+'</option>';
    //     }
    //     elem = elem + '</select>';
    //     $('#days-wrap').html(elem);
    //     $('#no-days').select2();
    // }

    $(document).ready(function() {
        $('.data-table').DataTable();
        // $('.select').select2();
        // $('.select-ns').select2({
        //     minimumResultsForSearch: Infinity,
        // });

        // switchfunc($('#ltype'));

        // $('#ltype').on('change',function(e){
        //     switchfunc($(this));
        // });

        // @if(Laratrust::can('create-leave'))
        // $(document).on('click', '#add-btn', function(e){

		// 	e.preventDefault();

		// 	var btn = $(this),
		// 		btn_text = btn.html(),
		// 		ltype = $("#ltype").val(),
		// 		start_date = $("#start-date").val(),
		// 		nodays = $("#no-days").val(),
		// 		token ='{{ Session::token() }}',
		// 		url = "{{route('portal.leave.store')}}";
		// 		redir = "{{route('portal.leave.edit', ':id')}}";

		// 	$.ajax({
		// 		type: "POST",
		// 		url: url,
		// 		data: {
		// 			ltype: ltype,
		// 			nodays: nodays,
		// 			start_date: start_date,
		// 			_token: token
		// 		},
		// 		beforeSend: function () {
		// 			btn.html('<i class="fas fa-spinner fa-spin"></i>');
		// 		},
		// 		success: function(response) {
        //             redir = redir.replace(':id', response.msg);
        //             window.location.href = redir;
		// 		},
		// 		error: function(jqXHR, exception){
		// 			btn.html(btn_text);
		// 			var error = getErrorMessage(jqXHR, exception);
        //             swal_alert('Failed to create leave',error,'error','Go Back');
		// 		}
		// 	});
        // });
        // @endif

    });

</script>

@endsection
