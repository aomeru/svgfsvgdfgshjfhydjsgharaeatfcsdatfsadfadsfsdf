@extends('layouts.portal')
@section('page_title','Leave Allocation - ')
@section('portal_page_title') <i class="fas fa-calendar mr-3"></i>Leave Allocation @endSection

@section('bc')
    <nav aria-label="breadcrumb" class="d-none d-md-block">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('portal')}}">Dashboard</a></li>
            <li class="breadcrumb-item">Leave</li>
            <li class="breadcrumb-item active" aria-current="page">Allocation</li>
        </ol>
    </nav>
@endSection

@section('content')

<div class="card">
    <div class="card-body">
        @if(Laratrust::can('create-leave-type'))
        <div class="mb-3 d-flex justify-content-end">
            <button class="btn btn-primary btn-sm no-margin" title="Create new leave type" data-toggle="modal" data-target="#add-modal"><i class="fas fa-user-plus mr-2"></i>Allocate</button>
        </div>
        @endif

        @if ($list->count() == 0)
            <div class="alert alert-info" role="role">No leave allocation record found.</div>
        @else

            <div class="table-responssive">

                <table class="table table-striped table-bordered table-hover nowwrap data-table" width="100%" data-page-length="25">

                    <thead>
                        <tr class="active">
                            <th>#</th>
                            <th>User</th>
                            @foreach($ltypes as $ltype)
                            <th class="text-center">{{$ltype->title}}</th>
                            @endforeach
                            @if(Laratrust::can('update-leave-allocation|delete-leave-allocation'))
                                <th class="text-right">Actions</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>

                        @php $row_count = 1 @endphp

                        @foreach($users as $item)

                            <tr>

                                <td>{{ $row_count }}</td>

                                <td>{{ $item->firstname.' '.$item->lastname }}</td>

                                @for($x=0;$x<$ltypes->count();$x++)
                                <?php
                                $rec = $item->leave_allocation()->where('leave_type_id',$ltypes[$x]['id'])->first();
                                ?>
                                <td class="text-center">
                                    {!! $rec == null ? '<span class="text-muted">N/A</span>' : $rec->allowed !!}
                                </td>
                                @endfor

                                @if(Laratrust::can('update-leave-allocation'))
                                <td class="text-right">
                                    @if(Laratrust::can('update-leave-type'))<a class="btn btn-primary btn-sm text-white" title="Edit {{ $item->title }}"><i class="fas fa-pencil-alt"></i></a>@endif
                                </td>
                                @endif

                            </tr>

                            @php $row_count++ @endphp

                        @endforeach

                    </tbody>

                </table>

            </div>

        @endif
    </div>
</div>

@endsection






@section('page_footer')

@if(Laratrust::can('create-leave-allocation'))
<div class="modal fade" id="add-modal" tabinndex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog sm-w500" role="document">
		<div class="modal-content">
            <form method="post">

				<div class="modal-header"><h5 class="modal-title">Allocate Employee Leave</h5></div>

				<div class="modal-body">

                    <div class="form-group">
                        <label for="users" class="form-control-label sr-onlyy">Employee(s)</label>
                        <select name="users[]" id="users" class="form-control select" style="width: 100%;" multiple>
                            <option>Select Employee(s)</option>
                            @foreach($users as $user)
                                <option value="{{$user->email}}">
                                    {{$user->firstname.' '.$user->lastname}}
                                    @if($user->unit != null)
                                        {{ ' - '.$user->unit->title}}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="ltypes" class="form-control-label sr-onlyy">Leave Tyes</label>
                        <select name="ltypes[]" id="ltypes" class="form-control select" style="width: 100%;" multiple>
                            <option>Select Leave Types</option>
                            @foreach($ltypes as $type)
                                <option value="{{$type->title}}">{{ $type->title}}</option>
                            @endforeach
                        </select>
                    </div>

				</div>

				<div class="modal-footer">
                    <input id="permmode" type="hidden" readonly value="basic">
                    <button type="button" class="btn-default btn" data-dismiss="modal" aria-label="Close"><i class="fas fa-times mr-2"></i>Cancel</button>
                    <button class="btn-primary btn" id='add-btn' type="submit" role="button"><i class="fas fa-check mr-2"></i>Allocate</button>
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
        $('.data-table').DataTable();
        $('.select').select2();
        $('.select-ns').select2({
            minimumResultsForSearch: Infinity,
        });

        // $('#add-modal').modal('show');

        @if(Laratrust::can('create-manager'))
        $(document).on('click', '#add-btn', function(e){

			e.preventDefault();

			var btn = $(this),
				btn_text = btn.html(),
				users = $("#users").val(),
				types = $("#ltypes").val(),
				token ='{{ Session::token() }}',
				url = "{{route('leave-allocation.store')}}";

			$.ajax({
				type: "POST",
				url: url,
				data: {
					users: users,
					types: types,
					_token: token
				},
				beforeSend: function () {
					btn.html('<i class="fas fa-spinner fa-spin"></i>');
				},
				success: function(response) {
					btn.html(btn_text);
                    $('#add-modal').modal('hide');
					swal_alert('Employee(s) leave allocation completed','','success','Continue');
                    window.setTimeout(function(){
                        window.location.href = "{{route('leave-allocation.index')}}";
                    },1000);
				},
				error: function(jqXHR, exception){
					btn.html(btn_text);
					var error = getErrorMessage(jqXHR, exception);
                    swal_alert('Failed to allocate leave to employees',error,'error','Go Back');
				}
			});
        });
        @endif

    });

</script>

@endsection
