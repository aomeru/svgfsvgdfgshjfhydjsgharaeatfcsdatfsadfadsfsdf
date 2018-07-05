@extends('layouts.portal')
@section('page_title','Holidays - ')
@section('portal_page_title') <i class="fas fa-plane mr-3"></i>Holidays @endSection

@section('bc')
    <nav aria-label="breadcrumb" class="d-none d-md-block">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('portal')}}">Dashboard</a></li>
            <li class="breadcrumb-item">Leave</li>
            <li class="breadcrumb-item active" aria-current="page">Holidays</li>
        </ol>
    </nav>
@endSection

@section('content')

<div class="card">
    <div class="card-body">
        @if(Laratrust::can('create-holiday'))
        <div class="mb-3 d-flex justify-content-end">
            <button class="btn btn-primary btn-sm no-margin" title="Create new holiday" data-toggle="modal" data-target="#add-modal"><i class="fas fa-plus"></i></button>
        </div>
        @endif

        @if ($list->count() == 0)
            <div class="alert alert-info" role="role">No role record found.</div>
        @else

            <div class="table-responssive">

                <table class="table table-striped table-bordered table-hover nowwrap data-table" width="100%" data-page-length="25">

                    <thead>
                        <tr class="active">
                            <th>#</th>
                            <th>Title</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th class="text-center">Modified</th>
                            @if(Laratrust::can('edit-holiday|delete-holiday'))<th class="text-right">Actions</th>@endif
                        </tr>
                    </thead>

                    <tbody>

                        @php $row_count = 1 @endphp

                        @foreach($list as $item)

                            <tr id="row-{{$item->id}}" data-hrid="{{$item->id}}" data-id="{{Crypt::encrypt($item->id)}}" data-title="{{$item->title}}" data-start-date="{{$item->start_date}}" data-end-date="{{$item->end_date}}">

                                <td>{{ $row_count }}</td>

                                <td>{{ $item->title }}</td>

                                <td>{{ $item->start_date }}</td>

                                <td>{!! $item->end_date == null ? '<em class="c-999">Null</em>' : $item->end_date !!}</td>

                                <td class="text-center">{{\Carbon\Carbon::parse($item->updated_at)->diffForHumans()}}</td>

                                @if(Laratrust::can('read-holiday|delete-holiday'))
                                <td class="text-right">
                                    @if(Laratrust::can('read-holiday'))<button class="btn btn-primary btn-sm" title="Edit {{ $item->title }} holiday" data-toggle="modal" data-target="#edit-modal"><i class="fas fa-pencil-alt"></i></button>@endif

                                    @if(Laratrust::can('delete-holiday'))<button class="btn btn-danger btn-sm" title="Delete {{ $item->display_name }} holiday" data-toggle="modal" data-target="#delete-modal"><i class="far fa-trash-alt"></i></button>@endif
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

@if(Laratrust::can('create-holiday'))
<div class="modal fade" id="add-modal" tabinndex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog w300 sm-w500" role="document">
		<div class="modal-content">
			<form method="post">

				<div class="modal-header">
					<h5 class="modal-title">Create Holiday</h5>
				</div>

				<div class="modal-body">
					<div class="form-group">
                        <label for="title" class="form-control-label sr-onlyy">Title/Type</label>

                        <input type="text" id="title" class="form-control" placeholder="Please enter holiday title" data-validation="custom required" data-validation-regexp="^([a-zA-Z0-9-'' ]*)$" data-validation-error-msg="Please use aplhanumeric characters only and hypen" maxlength="100">
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="start-date" class="form-control-label">Start Date</label>
                                <div class="input-group mb-3">
                                    <input id="start-date" type="date" class="form-control date-set" placeholder="Start Date">
                                    <label for="start-date" class="input-group-append mb-0">
                                        <span class="input-group-text"><span class="fas fa-calendar"></span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="end-date" class="form-control-label">End Date</label>
                                <div class="input-group mb-3">
                                    <input id="end-date" type="date" class="form-control date-input" placeholder="End Date">
                                    <label for="end-date" class="input-group-append mb-0">
                                        <span class="input-group-text"><span class="fas fa-calendar"></span></span>
                                    </label>
                                </div>
                            </div>
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

@if(Laratrust::can('update-holiday'))
<div class="modal fade active" id="edit-modal" tabinndex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog sm-w500" role="document">
		<div class="modal-content">
            <form method="post">

                @method('PUT')

				<div class="modal-header"><h5 class="modal-title">Update Holiday</h5></div>

				<div class="modal-body">
                    <div class="form-group">
                        <label for="title-edit" class="form-control-label sr-onlyy">Title/Type</label>

                        <input type="text" id="title-edit" class="form-control" placeholder="Please enter holiday title" data-validation="custom required" data-validation-regexp="^([a-zA-Z0-9-'' ]*)$" data-validation-error-msg="Please use aplhanumeric characters only and hypen" maxlength="100">
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="start-date-edit" class="form-control-label">Start Date</label>
                                <div class="input-group mb-3">
                                    <input id="start-date-edit" type="date" class="form-control date-set" placeholder="Start Date">
                                    <label for="start-date-edit" class="input-group-append mb-0">
                                        <span class="input-group-text"><span class="fas fa-calendar"></span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="end-date-edit" class="form-control-label">End Date</label>
                                <div class="input-group mb-3">
                                    <input id="end-date-edit" type="date" class="form-control date-input" placeholder="End Date">
                                    <label for="end-date-edit" class="input-group-append mb-0">
                                        <span class="input-group-text"><span class="fas fa-calendar"></span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
				</div>

				<div class="modal-footer">
                    <input type="hidden" id="holiday-row-id">
                    <input type="hidden" id="holiday-id-edit">
                    <button type="button" class="btn-default btn" data-dismiss="modal" aria-label="Close"><i class="fas fa-times mr-2"></i>Cancel</button>
                    <button class="btn-primary btn" id='edit-btn' type="submit" role="button"><i class="fas fa-check mr-2"></i>Update</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endif

@if(Laratrust::can('delete-holiday'))
<div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog w300 sm-w500" role="document">
		<div class="modal-content">
            <div class="modal-body">
                <p class="text-center font-18x no-bottom-margin">Are you sure you want to delete "<span id="delete-title" class="c-06f"></span>" holiday?</p>
            </div>

            <div class="modal-footer mh-override">
                <input type="hidden" id="holiday-row-id-delete">
                <input type="hidden" id="holiday-id-delete">
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

        // $('#add-modal').modal('show');
        $('.date-set').on('change',function(e){
            var elem = e.target,
                row = elem.closest('.row'),
                dinput = $(row).find('.date-input')[0];
            $(dinput).prop('min',elem.value);
        });

        @if(Laratrust::can('create-holiday'))
        $(document).on('click', '#add-btn', function(e){

			e.preventDefault();

			var btn = $(this),
				btn_text = btn.html(),
				title = $("#title").val(),
				start_date = $("#start-date").val(),
				end_date = $("#end-date").val(),
				token ='{{ Session::token() }}',
				url = "{{route('holiday.store')}}";

			$.ajax({
				type: "POST",
				url: url,
				data: {
					title: title,
					start_date: start_date,
					end_date: end_date,
					_token: token
				},
				beforeSend: function () {
					btn.html('<i class="fas fa-spinner fa-spin"></i>');
				},
				success: function(response) {
					btn.html(btn_text);
                    $('#add-modal').modal('hide');
					swal_alert('Holiday Created','','success','Continue');
                    window.setTimeout(function(){
                        window.location.href = "{{route('holiday.index')}}";
                    },1000);
				},
				error: function(jqXHR, exception){
					btn.html(btn_text);
					var error = getErrorMessage(jqXHR, exception);
                    swal_alert('Failed to Create Holiday',error,'error','Go Back');
				}
			});
        });
        @endif

        @if(Laratrust::can('update-leave-type'))
        $('#edit-modal').on('show.bs.modal', function (e) {
			var btn = $(e.relatedTarget),
				tr = btn.closest('tr'),
				title = tr.data('title'),
				start_date = tr.data('start-date'),
				end_date = tr.data('end-date'),
				hrid = tr.data('hrid'),
                item_id = tr.data('id');

			$("#title-edit").val(title);
			$("#start-date-edit").val(start_date).trigger('change');
			$("#end-date-edit").val(end_date).prop('min',start_date).trigger('change');
			$("#holiday-id-edit").val(item_id);
			$("#holiday-row-id").val(hrid);
		});

        $(document).on('click', '#edit-btn', function(e){

			e.preventDefault();

			var btn = $(this),
				btn_text = btn.html(),
				title = $("#title-edit").val(),
				start_date = $("#start-date-edit").val(),
				end_date = $("#end-date-edit").val(),
				load_element = "#row-" + $("#holiday-row-id").val(),
				token ='{{ Session::token() }}',
				url = "{{route('holiday.update', ':id')}}";
                url = url.replace(':id',$('#holiday-id-edit').val());

			$.ajax({
				type: "PUT",
				url: url,
				data: {
					title: title,
					start_date: start_date,
					end_date: end_date,
					_token: token
				},
				beforeSend: function () {
					btn.html('<i class="fa fa-spinner fa-spin"></i>');
				},
				success: function(response) {
					btn.html(btn_text);
					swal_alert('Holiday Updated', '', 'success', 'continue');
					$('#edit-modal').modal('hide');
					$(load_element).data('title',title);
					$(load_element).data('start-date',start_date);
					$(load_element).data('end-date',end_date);
					$(load_element).load(location.href + " "+ load_element +">*","");
				},
				error: function(jqXHR, exception){
					btn.html(btn_text);
					var error = getErrorMessage(jqXHR, exception);
					swal_alert('Failed to update Holiday',error,'error','Go Back');
				}
			});
		});
        @endif

        @if(Laratrust::can('delete-holiday'))
        $('#delete-modal').on('show.bs.modal', function (e) {
			var btn = $(e.relatedTarget),
				tr = btn.closest('tr'),
                delete_title = tr.data('title'),
				hrid = tr.data('hrid'),
				item_id = tr.data('id');

			$("#delete-title").text(delete_title);
			$("#holiday-id-delete").val(item_id);
			$("#holiday-row-id-delete").val(hrid);
		});

		$(document).on('click', '#delete-btn', function(e){
			e.preventDefault();
			var btn = $(this),
				btn_text = btn.html(),
				item_id = $('#holiday-id-delete').val(),

				remove_element = '#row-' + $("#holiday-row-id-delete").val(),
				load_element = '#loadDiv',
				token ='{{ Session::token() }}',
				url = "{{route('holiday.destroy', ':id')}}";
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
                    swal_alert('Holiday Deleted','','success','Continue');
					$(remove_element).remove();
					//$(load_element).load(location.href + " "+ load_element +">*","");
				},
				error: function(jqXHR, exception){
					btn.html(btn_text);
					var error = getErrorMessage(jqXHR, exception);
					swal_alert('Failed to Delete Holiday',error,'error','Go Back');
				}
			});
        });
        @endif
    });

</script>

@endsection
