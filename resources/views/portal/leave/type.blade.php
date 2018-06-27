@extends('layouts.portal')
@section('page_title','Leave Type - ')
@section('portal_page_title') <i class="far fa-calendar mr-3"></i>Leave Type @endSection

@section('bc')
    <nav aria-label="breadcrumb" class="d-none d-md-block">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('portal')}}">Dashboard</a></li>
            <li class="breadcrumb-item">Leave</li>
            <li class="breadcrumb-item active" aria-current="page">Types</li>
        </ol>
    </nav>
@endSection

@section('content')

<div class="card">
    <div class="card-body">
        @if(Laratrust::can('create-leave-type'))
        <div class="mb-3 d-flex justify-content-end">
            <button class="btn btn-primary btn-sm no-margin" title="Create new leave type" data-toggle="modal" data-target="#add-modal"><i class="fas fa-plus"></i></button>
        </div>
        @endif

        @if ($list->count() == 0)
            <div class="alert alert-info" role="role">No leave type record found.</div>
        @else

            <div class="table-responssive">

                <table class="table table-striped table-bordered table-hover nowwrap data-table" width="100%" data-page-length="25">

                    <thead>
                        <tr class="active">
                            <th>#</th>
                            <th>Title</th>
                            <th class="text-center">Year</th>
                            <th>Type</th>
                            <th class="text-center">Allowed</th>
                            <th class="text-center">Last Modified</th>
                            <th class="text-center">Status</th>
                            @if(Laratrust::can('update-leave-type|delete-leave-type'))<th class="text-right">Actions</th>@endif
                        </tr>
                    </thead>

                    <tbody>

                        @php $row_count = 1 @endphp

                        @foreach($list as $item)

                            <tr id="row-{{$item->id}}" data-hrid="{{$item->id}}" data-id="{{Crypt::encrypt($item->id)}}" data-year="{{ substr($item->title,-4) }}" data-title="{{str_replace(substr($item->title,-4),'',$item->title).substr($item->title,-4)}}" data-type="{{$item->type}}" data-allowed="{{$item->type == 'static'? round($item->allowed) : $item->allowed}}" data-status="{{$item->status}}">

                                <td>{{ $row_count }}</td>

                                <td>{{ $item->title }}</td>

                                <td class="text-center">{{ substr($item->title,-4) }}</td>

                                <td class="text-uppercase">{{ $item->type }}</td>

                                <td class="text-center">{{ $item->type == 'calculated' ? 'M * '.$item->allowed : round($item->allowed).' day(s)' }}</td>

                                <td class="text-center">{{ date('jS M Y, h:ia',strtotime($item->updated_at)) }} | <span class="text-muted">{{ $item->user->firstname.' '.$item->user->lastname }}</span></td>

                                <td class="text-capitalize text-center">{{ $item->status }}</td>

                                @if(Laratrust::can('update-leave-type|delete-leave-type'))
                                <td class="text-right">
                                    @if(Laratrust::can('update-leave-type'))<button class="btn btn-primary btn-sm" title="Edit {{ $item->title }}" data-toggle="modal" data-target="#edit-modal"><i class="fas fa-pencil-alt"></i></button>@endif
                                    @if(Laratrust::can('delete-leave-type'))<button class="btn btn-danger btn-sm" title="Delete {{ $item->title }}" data-toggle="modal" data-target="#delete-modal"><i class="far fa-trash-alt"></i></button>@endif
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

@if(Laratrust::can('create-leave-type'))
<div class="modal fade active" id="add-modal" tabinndex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog sm-w500" role="document">
		<div class="modal-content">
            <form method="post">

				<div class="modal-header"><h5 class="modal-title">Create Leave Type</h5></div>

				<div class="modal-body">
                    <div class="row">
                        <div class="col-4 offset-4">
                            <div class="form-group">
                                <select name="status" id="status" class="select" style="width: 100%">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <select name="year" id="year" class="select" style="width: 100%">
                                    @for($x=2017;$x<=2050;$x++)
                                        <option value="{{$x}}" @if($x == config('app.leave_year')) selected @endif>{{$x}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    <hr class="mb-3 mt-0">
                    <div class="form-group">
                        <label for="title" class="form-control-label sr-onlyy">Leave Title</label>

                        <input type="text" id="title" class="form-control" placeholder="Please enter a leave type title" data-validation="custom required" data-validation-regexp="^([a-zA-Z0-9- ]+)$" data-validation-error-msg="Please use aplhanumeric characters only and hypen" maxlength="100">
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="type" class="form-control-label">Type</label>

                                <select name="type" id="type" class="select-ns" style="width: 100%">
                                    <option value="calculated">Calculated</option>
                                    <option value="static" selected>Static</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="allowed" class="form-control-label">Allowed</label>

                                <select name="allowed" id="allowed" class="select" style="width: 100%">
                                    @for($x=1;$x<=60;$x++)
                                        <option value="{{$x}}">{{$x}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="callowed" class="form-control-label">Calculated Value</label>
                                <input type="text" id="callowed" class="form-control" placeholder="Please enter module" data-validation="custom" data-validation-regexp="^([0-9.]*)$" data-validation-error-msg="Please use aplhanumeric characters only and hypen" value="0">
                            </div>
                        </div>
                        <div class="col-6">
                            <p class="form-text text-muted">
                                Please set "Type" to Calculated to enter the value months will be multiplied by
                            </p>
                        </div>
                    </div>
				</div>

				<div class="modal-footer">
                    <button type="button" class="btn-default btn" data-dismiss="modal" aria-label="Close"><i class="fas fa-times mr-2"></i>Cancel</button>
                    <button class="btn-primary btn" id='add-btn' type="submit" role="button"><i class="fas fa-check mr-2"></i>Create</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endif


@if(Laratrust::can('update-leave-type'))
<div class="modal fade active" id="edit-modal" tabinndex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog sm-w500" role="document">
		<div class="modal-content">
            <form method="post">

                @method('PUT')

				<div class="modal-header"><h5 class="modal-title">Update Leave Type</h5></div>

				<div class="modal-body">
                    <div class="row">
                        <div class="col-4 offset-4">
                            <div class="form-group">
                                <select name="status_edit" id="status-edit" class="form-control select" style="width: 100%">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <select name="year-edit" id="year-edit" class="select" style="width: 100%">
                                    @for($x=2017;$x<=2050;$x++)
                                        <option value="{{$x}}">{{$x}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    <hr class="mb-3 mt-0">
                    <div class="form-group">
                        <label for="title-edit" class="form-control-label sr-onlyy">Leave Title</label>

                        <input type="text" id="title-edit" class="form-control" placeholder="Please enter a leave type title" data-validation="custom required" data-validation-regexp="^([a-zA-Z0-9- ]+)$" data-validation-error-msg="Please use aplhanumeric characters only and hypen" maxlength="100">
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="type-edit" class="form-control-label">Type</label>

                                <select id="type-edit" class="select-ns" style="width: 100%">
                                    <option value="calculated">Calculated</option>
                                    <option value="static">Static</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="allowed-edit" class="form-control-label">Allowed</label>

                                <select id="allowed-edit" class="form-control select" style="width: 100%">
                                    @for($x=1;$x<=60;$x++)
                                        <option value="{{$x}}">{{$x}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="callowed-edit" class="form-control-label">Calculated Value</label>
                                <input type="text" id="callowed-edit" class="form-control" placeholder="Please enter module" data-validation="custom" data-validation-regexp="^([0-9.]*)$" data-validation-error-msg="Please use aplhanumeric characters only and hypen">
                            </div>
                        </div>
                        <div class="col-6">
                            <p class="form-text text-muted">
                                Please set "Type" to Calculated to enter the value months will be multiplied by
                            </p>
                        </div>
                    </div>
				</div>

				<div class="modal-footer">
                    <input type="hidden" id="type-row-id">
                    <input type="hidden" id="type-id-edit">
                    <button type="button" class="btn-default btn" data-dismiss="modal" aria-label="Close"><i class="fas fa-times mr-2"></i>Cancel</button>
                    <button class="btn-primary btn" id='edit-btn' type="submit" role="button"><i class="fas fa-check mr-2"></i>Update</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endif



@if(Laratrust::can('delete-leave-type'))
<div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog w300 sm-w500" role="document">
		<div class="modal-content">
            <div class="modal-body">
                <p class="text-center font-18x no-bottom-margin">Are you sure you want to delete "<span id="delete-title" class="c-06f"></span>" leave type?</p>
            </div>

            <div class="modal-footer mh-override">
                <input type="hidden" id="type-row-id-delete">
                <input type="hidden" id="type-id-delete">
                <button type="button" class="btn-primary btn" data-dismiss="modal" aria-label="Close"><i class="fas fa-times mr5"></i>Cancel</button>
                <button class="btn-danger btn" id='delete-btn' type="submit" role="button"><i class="fas fa-check mr5"></i>Delete</button>
            </div>
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

        function switch_mode(mode = '',type='add')
        {
            if(type == 'add')
            {
                if(mode == 'calculated')
                {
                    $('#allowed').prop('disabled',true); $('#callowed').prop('readonly',false);
                } else {
                    $('#allowed').prop('disabled',false); $('#callowed').prop('readonly',true);
                }
            } else {
                if(mode == 'calculated')
                {
                    $('#allowed-edit').prop('disabled',true); $('#callowed-edit').prop('readonly',false);
                } else {
                    $('#allowed-edit').prop('disabled',false); $('#callowed-edit').prop('readonly',true);
                }
            }
        }
        switch_mode();
        $(document).on('change', '#type', function(e){ switch_mode($('#type').val()); });
        $(document).on('change', '#type-edit', function(e){ switch_mode($('#type-edit').val(),'edit'); });

        @if(Laratrust::can('create-leave-type'))
        $(document).on('click', '#add-btn', function(e){

			e.preventDefault();

			var btn = $(this),
				btn_text = btn.html(),
				title = $("#title").val(),
				year = $("#year").val(),
				type = $("#type").val(),
				allowed = $("#allowed").val(),
				callowed = $("#callowed").val(),
				status = $("#status").val(),
				token ='{{ Session::token() }}',
				url = "{{route('leave-type.store')}}";

			$.ajax({
				type: "POST",
				url: url,
				data: {
					title: title + ' ' + year,
					year: year,
					type: type,
					allowed: allowed,
					callowed: callowed,
					status: status,
					_token: token
				},
				beforeSend: function () {
					btn.html('<i class="fas fa-spinner fa-spin"></i>');
				},
				success: function(response) {
					btn.html(btn_text);
                    $('#add-modal').modal('hide');
					swal_alert('Leave Type Created','','success','Continue');
                    window.setTimeout(function(){
                        window.location.href = "{{route('leave-type.index')}}";
                    },1000);
				},
				error: function(jqXHR, exception){
					btn.html(btn_text);
					var error = getErrorMessage(jqXHR, exception);
                    swal_alert('Failed to create Leave Type',error,'error','Go Back');
				}
			});
        });
        @endif


        @if(Laratrust::can('update-leave-type'))
        $('#edit-modal').on('show.bs.modal', function (e) {
			var btn = $(e.relatedTarget),
				tr = btn.closest('tr'),
				year = tr.data('year'),
				title = tr.data('title'),
				type = tr.data('type'),
				allowed = tr.data('allowed'),
				status = tr.data('status'),
				hrid = tr.data('hrid'),
                item_id = tr.data('id');

			$("#year-edit").val(year).trigger('change');
			$("#title-edit").val(title);
			$("#type-edit").val(type).trigger('change');
			$("#status-edit").val(status).trigger('change');
            if(type != 'calculated') $("#allowed-edit").val(allowed).trigger('change'); else $("#callowed-edit").val(allowed);
			$("#type-id-edit").val(item_id);
			$("#type-row-id").val(hrid);
            switch_mode($('#type-edit').val(),'edit');
		});

        $(document).on('click', '#edit-btn', function(e){

			e.preventDefault();

			var btn = $(this),
				btn_text = btn.html(),
				status = $("#status-edit").val(),
				year = $("#year-edit").val(),
				title = $("#title-edit").val(),
				type = $("#type-edit").val(),
				allowed = $("#allowed-edit").val(),
				callowed = $("#callowed-edit").val(),
				load_element = "#urow-" + $("#type-row-id").val(),
				token ='{{ Session::token() }}',
				url = "{{route('leave-type.update', ':id')}}";
                url = url.replace(':id',$('#type-id-edit').val());

			$.ajax({
				type: "PUT",
				url: url,
				data: {
					status: status,
					year: year,
					title: title,
					type: type,
					allowed: allowed,
					callowed: callowed,
					_token: token
				},
				beforeSend: function () {
					btn.html('<i class="fa fa-spinner fa-spin"></i>');
				},
				success: function(response) {
					btn.html(btn_text);
					swal_alert('Unit Updated', '', 'success', 'continue');
					$('#edit-modal').modal('hide');
					$(load_element).data('status',status);
					$(load_element).data('year',year);
					$(load_element).data('title',title);
					$(load_element).data('type',type);
					$(load_element).data('allowed',allowed);
					$(load_element).data('callowed',callowed);
					$(load_element).load(location.href + " "+ load_element +">*","");
				},
				error: function(jqXHR, exception){
					btn.html(btn_text);
					var error = getErrorMessage(jqXHR, exception);
					swal_alert('Failed to update Leave Type',error,'error','Go Back');
				}
			});
		});
        @endif


        @if(Laratrust::can('delete-leave-type'))
        $('#delete-modal').on('show.bs.modal', function (e) {
			var btn = $(e.relatedTarget),
				tr = btn.closest('tr'),
                delete_title = tr.data('title'),
				hrid = tr.data('hrid'),
				item_id = tr.data('id');

			$("#delete-title").text(delete_title);
			$("#type-id-delete").val(item_id);
			$("#type-row-id-delete").val(hrid);
		});

		$(document).on('click', '#delete-btn', function(e){
			e.preventDefault();
			var btn = $(this),
				btn_text = btn.html(),
				item_id = $('#type-id-delete').val(),

				remove_element = '#row-' + $("#type-row-id-delete").val(),
				load_element = '#loadDiv',
				token ='{{ Session::token() }}',
				url = "{{route('leave-type.destroy', ':id')}}";
                url = url.replace(':id', item_id);

			$.ajax({
				type: "DELETE",
				url: url,
				data: {
					item_id: item_id,
					_token: token
				},
				beforeSend: function () {
					btn.html('<i class="fas fa-spinner fa-spin"></i>');
				},
				success: function(response) {
					btn.html(btn_text);
                    $('#delete-modal').modal('hide');
                    swal_alert('Leave Type deleted','','success','Continue');
					$(remove_element).remove();
					//$(load_element).load(location.href + " "+ load_element +">*","");
				},
				error: function(jqXHR, exception){
					btn.html(btn_text);
					var error = getErrorMessage(jqXHR, exception);
					swal_alert('Failed to delete Leave Type',error,'error','Go Back');
				}
			});
        });
        @endif

    });

</script>

@endsection
