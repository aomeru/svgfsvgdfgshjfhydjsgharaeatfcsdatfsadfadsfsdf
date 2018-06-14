@extends('layouts.portal')
@section('page_title','Departments & Units - ')
@section('portal_page_title') <i class="fas fa-university mr-3"></i>Departments &amp; Units @endSection

@section('bc')
    <nav aria-label="breadcrumb" class="d-none d-md-block">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('portal')}}">Dashboard</a></li>
            <li class="breadcrumb-item">Settings</li>
            <li class="breadcrumb-item active" aria-current="page">Departments &amp; Units</li>
        </ol>
    </nav>
@endSection


@section('content')

   <div id="loadDiv" class="row">

        <div class="col-sm-6">
            <div class="card">
                <h5 class="card-header bgc-555 c-fff">Departments</h5>
                <div class="card-body">
                    <div class="mb-3 d-flex justify-content-end">
                        <button class="btn btn-primary btn-sm no-margin" title="Add new department" data-toggle="modal" data-target="#add-dept-modal"><i class="fa fa-plus"></i></button>
                    </div>

                    @if ($depts->count() == 0)
                        <p class="alert alert-info">No department record found.</p>
                    @else

                        <div class="table-responsive">

                            <table id="dept-table" class="data-table table table-striped table-bordered table-hover nowrap" width="100%" data-page-length="10">

                                <thead>
                                    <tr class="active">
                                        <th>#</th>
                                        <th>Title</th>
                                        <th class="text-center">ED / GM</th>
                                        <th class="text-center">Units</th>
                                        <th class="text-center">Staff</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @php $row_count = 1 @endphp

                                    @foreach($depts as $item)
                                    <?php
                                    $type = '';
                                    $head = '';
                                    if($item->ed_id != null){ $type = 'ed'; $head = $item->ed->email; }elseif($item->gm_id != null){ $type = 'gm'; $head = $item->gm->email; }
                                    ?>

                                        <tr id="row-{{$item->id}}" data-hrid="{{$item->id}}" data-item-id="{{Crypt::encrypt($item->id)}}" data-item-title="{{$item->title}}" data-item-head-type="{{$type}}" data-item-head="{{$head == '' ? '' : $head}}">
                                            <td>{{ $row_count }}</td>
                                            <td>
                                                <u><a href="{{route('portal.depts.show', Crypt::encrypt($item->id))}}" class="c-06f">{{ $item->title }}</a></u>
                                            </td>
                                            <td>
                                                @if($item->gm != null)
                                                    {{$item->gm->firstname.' '.$item->gm->lastname}}
                                                @elseif($item->ed != null)
                                                    {{$item->ed->firstname.' '.$item->ed->lastname}}
                                                @else
                                                    <em class="c-666">Null</em>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $item->units->count() }}</td>
                                            <td class="text-center">
                                                <?php
                                                $ds_count = 0;
                                                foreach($item->units as $un)
                                                {
                                                    $ds_count += $un->users->count();
                                                }
                                                ?>
                                                {{$ds_count}}
                                            </td>
                                            <td class="text-center">
                                                <button class="edit-dept-btn btn btn-primary btn-sm" title="Edit {{ $item->title }}" data-toggle="modal" data-target="#edit-dept-modal"><i class="fas fa-pencil-alt"></i></button>
                                                <button class="btn btn-danger btn-sm" title="Delete {{ $item->title }}" data-toggle="modal" data-target="#delete-dept-modal"><i class="far fa-trash-alt"></i></button>
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

        <div class="col-sm-6">
            <div class="card">
                <h5 class="card-header bgc-555 c-fff">Units</h5>
                <div class="card-body">
                    <div class="mb-3 d-flex justify-content-end">
                        <button class="btn btn-primary btn-sm no-margin" title="Add new sub unit" data-toggle="modal" data-target="#add-unit-modal"><i class="fa fa-plus"></i></button>
                    </div>

                    @if ($units->count() == 0)
                        <p class="alert alert-info">No sub unit record found.</p>
                    @else

                        <div class="table-responsive">

                            <table id="unit-table" class="data-table table table-striped table-bordered table-hover nowrap" width="100%" data-page-length="10">

                                <thead>
                                    <tr class="actiive">
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Department</th>
                                        <th>Manager</th>
                                        <th class="text-center">Staff</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @php $row_count = 1 @endphp

                                    @foreach($units as $unit)

                                        <tr id="urow-{{$unit->id}}" data-hrid="{{$unit->id}}" data-item-id="{{Crypt::encrypt($unit->id)}}" data-item-title="{{$unit->title}}" data-item-dtitle="{{$unit->department->title}}" data-item-manager="{{$unit->manager == null ? '' : $unit->manager->email}}">
                                            <td>{{ $row_count }}</td>
                                            <td><u><a href="{{route('portal.depts.show.unit', Crypt::encrypt($unit->id))}}" class="c-06f">{{ $unit->title }}</a></u></td>
                                            <td>{{ $unit->department->title }}</td>
                                            <td>
                                                @if($unit->manager != null)
                                                    {{$unit->manager->firstname.' '.$unit->manager->lastname}}
                                                @else
                                                    <em class="c-666">Null</em>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $unit->users->count() }}</td>
                                            <td class="text-center">
                                                <button class="btn btn-primary btn-sm" title="Edit {{ $unit->title }}" data-toggle="modal" data-target="#edit-unit-modal"><i class="fas fa-pencil-alt"></i></button>
                                                <button class="btn btn-danger btn-sm" title="Delete {{ $unit->title }}" data-toggle="modal" data-target="#delete-unit-modal"><i class="far fa-trash-alt"></i></button>
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

    </div>

@endsection






@section('page_footer')
<div class="modal fade" id="add-dept-modal" tabinndex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog w300 sm-w500" role="document">
		<div class="modal-content">
			<form method="post">

				<div class="modal-header">
					<h5 class="modal-title font-weight-bold">Add Department</h5>
				</div>

				<div class="modal-body">
					<div class="form-group">
						<label for="dept-name" class="form-control-label">Department Name</label>

						<input type="text" name="dept_name" id="dept-name" class="form-control" value="{{ Request::old('dept_name') }}" placeholder="Enter departmental title" data-validation="custom required" data-validation-regexp="^([a-zA-Z&' ]+)$" data-validation-error-msg="Please use aplhanumeric characters only, with spaces and &amp;">
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label for="head-type" class="col-4 form-control-label">Head Type</label>

                            <div class="col-8">
                                <select id="head-type" class="form-control select-ns" style="width: 100%;">
                                    <option value="ed">Executive Director</option>
                                    <option value="gm">General Manager</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label for="head-value" class="form-control-label col-4">ED / GM</label>

                            <div class="col-8">
                                <select id="head-value" class="form-control select" style="width: 100%;">
                                    <option value="">Select Department Head</option>
                                    @foreach($users as $user)
                                        <option value="{{$user->email}}">{{$user->firstname.' '.$user->lastname}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
					</div>
				</div>

				<div class="modal-footer">
                    <button type="button" class="btn-default btn" data-dismiss="modal" aria-label="Close"><i class="fa fa-times mr5"></i>Cancel</button>
                    <button class="btn-primary btn" id='add-dept-btn' type="submit" role="button"><i class="fa fa-check mr5"></i>Add</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="edit-dept-modal" tabinndex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog w300 sm-w500" role="document">
		<div class="modal-content">
			<form method="post">

				<div class="modal-header">
					<h5 class="modal-title font-weight-bold">Edit Department</h5>
				</div>

				<div class="modal-body">
					<div class="form-group input_field_sections">
						<label for="dept-name-edit" class="form-control-label text-center">Department Name</label>

						<input type="text" id="dept-name-edit" class="form-control" placeholder="Enter departmental title" data-validation="custom required" data-validation-regexp="^([a-zA-Z&' ]+)$" data-validation-error-msg="Please use aplhanumeric characters only, with spaces and &amp;">
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label for="dept-head-type-edit" class="col-4 form-control-label">Head Type</label>

                            <div class="col-8">
                                <select id="dept-head-type-edit" class="select-ns form-control" style="width: 100%;">
                                    <option value="ed">Executive Director</option>
                                    <option value="gm">General Manager</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label for="dept-head-value-edit" class="form-control-label col-4">ED / GM</label>

                            <div class="col-8">
                                <select id="dept-head-value-edit" class="select form-control" style="width: 100%;">
                                    <option value="">Select Department Head</option>
                                    @foreach($users as $user)
                                        <option value="{{$user->email}}">{{$user->firstname.' '.$user->lastname}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
					</div>
				</div>

				<div class="modal-footer">
                    <input type="hidden" id="dept-row-id">
                    <input type="hidden" id="dept-id-edit">
                    <button type="button" class="btn-default btn" data-dismiss="modal" aria-label="Close"><i class="fa fa-times mr5"></i>Cancel</button>
                    <button class="btn-success btn" id='update-dept-btn' type="submit" role="button"><i class="fa fa-check mr5"></i>Update</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="delete-dept-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog w300 sm-w500" role="document">
		<div class="modal-content">
            <div class="modal-body">
                <p class="text-center font-18x no-bottom-margin">Are you sure you want to delete "<span id="delete-title" class="c-06f"></span>" department?</p>
            </div>

            <div class="modal-footer mh-override">
                <input type="hidden" id="dept-row-id-delete">
                <input type="hidden" id="dept-id-delete">
                <button type="button" class="btn-primary btn" data-dismiss="modal" aria-label="Close"><i class="fa fa-times mr5"></i>Cancel</button>
                <button class="btn-danger btn" id='delete-dept-btn' type="submit" role="button"><i class="fa fa-check mr5"></i>Delete</button>
            </div>
		</div>
	</div>
</div>

<div class="modal fade" id="add-unit-modal" tabinndex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog w300 sm-w500" role="document">
		<div class="modal-content">
			<form method="post">

				<div class="modal-header mh-override">
					<h5 class="modal-title font-weight-bold">Add Sub Unit</h5>
				</div>

				<div class="modal-body">
					<div class="form-group input_field_sections">
						<label for="unit-name" class="form-control-label">Unit Name</label>

						<input type="text" id="unit-name" class="form-control" placeholder="Enter sub unit name" data-validation="custom required" data-validation-regexp="^([a-zA-Z&' ]+)$" data-validation-error-msg="Please use aplhanumeric characters only, with spaces and &amp;">
					</div>

					<div class="form-group input_field_sections">
						<div class="row">
                            <label for="unit-dept-id" class="form-control-label col-4">Department</label>

                            <div class="col-8">
                                <select id="unit-dept-id" class="form-control select" style="width: 100%;">
                                    <option value="">Select Department</option>
                                    @foreach($depts as $dept)
                                        <option value="{{Crypt::encrypt($dept->id)}}">{{$dept->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label for="unit-manager" class="form-control-label col-4">Unit Manager</label>

                            <div class="col-8">
                                <select id="unit-manager" class="form-control select" style="width: 100%;">
                                    <option value="">Select Unit Manager</option>
                                    @foreach($users as $user)
                                        <option value="{{$user->email}}">{{$user->firstname.' '.$user->lastname}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
					</div>
				</div>

				<div class="modal-footer mh-override">
                    <button type="button" class="btn-default btn" data-dismiss="modal" aria-label="Close"><i class="fa fa-times mr5"></i>Cancel</button>
                    <button class="btn-success btn" id='add-unit-btn' type="submit" role="button"><i class="fa fa-check mr5"></i>Add</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="edit-unit-modal" tabinndex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog w300 sm-w500" role="document">
		<div class="modal-content">
			<form method="post">

				<div class="modal-header mh-override">
					<h5 class="modal-title font-weight-bold">Edit Sub Unit</h5>
				</div>

				<div class="modal-body">
					<div class="form-group input_field_sections">
						<label for="unit-name-edit" class="form-control-label text-center">Unit Name</label>

						<input type="text" id="unit-name-edit" class="form-control" placeholder="Enter sub unit name" data-validation="custom required" data-validation-regexp="^([a-zA-Z&' ]+)$" data-validation-error-msg="Please use aplhanumeric characters only, with spaces and &amp;">
					</div>

					<div class="form-group input_field_sections">
						<div class="row">
                            <label for="unit-dept-title-edit" class="form-control-label col-4">Department</label>

                            <div class="col-8">
                                <select id="unit-dept-title-edit" class="form-control select" style="width: 100%;">
                                    <option value="">Select Department</option>
                                    @foreach($depts as $dept)
                                        <option value="{{$dept->title}}">{{$dept->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                     <div class="form-group">
                        <div class="row">
                            <label for="unit-manager-edit" class="form-control-label col-4">Unit Manager</label>

                            <div class="col-8">
                                <select id="unit-manager-edit" class="form-control select" style="width: 100%;">
                                    <option value="">Select Unit Manager</option>
                                    @foreach($users as $user)
                                        <option value="{{$user->email}}">{{$user->firstname.' '.$user->lastname}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
					</div>
				</div>

				<div class="modal-footer mh-override">
                    <input type="hidden" id="unit-id-edit">
                    <input type="hidden" id="unit-row-id">
                    <button type="button" class="btn-default btn" data-dismiss="modal" aria-label="Close"><i class="fa fa-times mr5"></i>Cancel</button>
                    <button class="btn-success btn" id='update-unit-btn' type="submit" role="button"><i class="fa fa-check mr5"></i>Update</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="delete-unit-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog w300 sm-w500" role="document">
		<div class="modal-content">

            <div class="modal-body">

                <p class="text-center font-18x no-bottom-margin">Are you sure you want to delete "<span id="delete-unit-title" class="c-06f"></span>" unit?</p>

            </div>

            <div class="modal-footer mh-override">
                <input type="hidden" id="unit-row-id-delete">
                <input type="hidden" id="unit-id-delete">
                <button type="button" class="btn-primary btn" data-dismiss="modal" aria-label="Close"><i class="fa fa-times mr5"></i>Cancel</button>
                <button class="btn-danger btn" id='delete-unit-btn' type="submit" role="button"><i class="fa fa-check mr5"></i>Delete</button>
            </div>
		</div>
	</div>
</div>
@endsection







@section('scripts')

<script>
    $(document).ready(function() {
        $('.data-table').DataTable();
        $('.select').select2();
        $('.select-ns').select2({
            minimumResultsForSearch: Infinity,
        });

        $(document).on('click', '#add-dept-btn', function(e){

			e.preventDefault();

			var btn = $(this),
				btn_text = btn.html(),
				dept_name = $("#dept-name").val(),
				head_type = $("#head-type").val(),
				head_value = $("#head-value").val(),
				token ='{{ Session::token() }}',
				url = "{{route('portal.depts.add')}}";

			$.ajax({
				type: "POST",
				url: url,
				data: {
					dept_name: dept_name,
					head_type: head_type,
					head_value: head_value,
					_token: token
				},
				beforeSend: function () {
					btn.html('<i class="fas fa-spinner fa-spin"></i>');
				},
				success: function(response) {
					btn.html(btn_text);
                    $('#add-dept-modal').modal('hide');
					swal_alert('Department Created','Department has been created','success','Continue');
                    window.setTimeout(function(){
                        window.location.href = "{{route('portal.depts')}}";
                    },1000);
				},
				error: function(jqXHR, exception){
					btn.html(btn_text);
					var error = getErrorMessage(jqXHR, exception);
                    swal_alert('Failed to Create Department',error,'error','Go Back');
				}
			});
        });

        $('#edit-dept-modal').on('show.bs.modal', function (e) {
			var btn = $(e.relatedTarget),
				tr = btn.closest('tr'),
				title = tr.data('item-title'),
				hrid = tr.data('hrid'),
				head_type = tr.data('item-head-type'),
				head_id = tr.data('item-head'),
				item_id = tr.data('item-id');

			$("#dept-name-edit").val(title);
            if(head_type == '') head_type = 'ed';
            $("#dept-head-type-edit").val(head_type).trigger('change');
            // $('#dept-head-type-edit').select2().select2('val',head_type);
			$("#dept-head-value-edit").val(head_id).trigger('change');
            // $('#dept-head-value-edit').select2().select2('val',head_id);
			$("#dept-id-edit").val(item_id);
			$("#dept-row-id").val(hrid);
		});

		$(document).on('click', '#update-dept-btn', function(e){

			e.preventDefault();

			var btn = $(this),
				btn_text = btn.html(),
				dept_name = $("#dept-name-edit").val(),
				dept_id = $("#dept-id-edit").val(),
				head_type = $("#dept-head-type-edit").val(),
				head_value = $("#dept-head-value-edit").val(),
				load_element = "#row-" + $("#dept-row-id").val(),
				token ='{{ Session::token() }}',
				url = "{{route('portal.depts.update')}}";

			$.ajax({
				type: "POST",
				url: url,
				data: {
					dept_name: dept_name,
					dept_id: dept_id,
					head_type: head_type,
					head_value: head_value,
					_token: token
				},
				beforeSend: function () {
					btn.html('<i class="fa fa-spinner fa-spin"></i>');
				},
				success: function(response) {
					btn.html(btn_text);
                    $('#edit-dept-modal').modal('hide');
                    swal_alert('Department Updated','Department has been updated','success','Continue');
					$(load_element).data('item-title',dept_name);
					$(load_element).data('item-head-type',head_type);
					$(load_element).data('item-head',head_value);
					$(load_element).load(location.href + " "+ load_element +">*","");
				},
				error: function(jqXHR, exception){
					btn.html(btn_text);
                    var error = getErrorMessage(jqXHR, exception);
                    swal_alert('Failed to Update Department',error,'error','Go Back');
				}
			});
        });

        $('#delete-dept-modal').on('show.bs.modal', function (e) {
			var btn = $(e.relatedTarget),
				tr = btn.closest('tr'),
				delete_title = tr.data('item-title'),
				hrid = tr.data('hrid'),
				item_id = tr.data('item-id');

			$("#delete-title").text(delete_title);
			$("#dept-id-delete").val(item_id);
			$("#dept-row-id-delete").val(hrid);
		});

		$(document).on('click', '#delete-dept-btn', function(e){
			e.preventDefault();
			var btn = $(this),
				btn_text = btn.html(),
				item_id = $('#dept-id-delete').val(),

				remove_element = '#row-' + $("#dept-row-id-delete").val(),
				load_element = '#loadDiv',
				token ='{{ Session::token() }}',
				url = "{{route('portal.depts.delete')}}";

			$.ajax({
				type: "POST",
				url: url,
				data: {
					item_id: item_id,
					_token: token
				},
				beforeSend: function () {
					btn.html('<i class="fa fa-spinner fa-spin"></i>');
				},
				success: function(response) {
					btn.html(btn_text);
                    $('#delete-dept-modal').modal('hide');
                    swal_alert('Department Updated',response.message,'success','Continue');
					$(remove_element).remove();
					//$(load_element).load(location.href + " "+ load_element +">*","");
				},
				error: function(jqXHR, exception){
					btn.html(btn_text);
					var error = getErrorMessage(jqXHR, exception);
					swal_alert('Failed to Delete Department',error,'error','Go Back');
				}
			});
        });

        $(document).on('click', '#add-unit-btn', function(e){

			e.preventDefault();

			var btn = $(this),
				btn_text = btn.html(),
				unit_name = $("#unit-name").val(),
				unit_dept_id = $("#unit-dept-id").val(),
				unit_manager = $("#unit-manager").val(),
				token ='{{ Session::token() }}',
				url = "{{route('portal.depts.add.unit')}}";

			$.ajax({
				type: "POST",
				url: url,
				data: {
					unit_name: unit_name,
					unit_dept_id: unit_dept_id,
					unit_manager: unit_manager,
					_token: token
				},
				beforeSend: function () {
					btn.html('<i class="fa fa-spinner fa-spin"></i>');
				},
				success: function(response) {
					btn.html(btn_text);
					swal_alert('Unit Created','','success','Continue');
					$('#add-unit-modal').modal('hide');
					window.setTimeout(function(){ window.location.href = "{{route('portal.depts')}}";}, 1000);
				},
				error: function(jqXHR, exception){
					btn.html(btn_text);
					var error = getErrorMessage(jqXHR, exception);
                    swal_alert('Failed to Create Unit',error,'error','Go Back');
				}
			});
		});

		$('#edit-unit-modal').on('show.bs.modal', function (e) {
			var btn = $(e.relatedTarget),
				tr = btn.closest('tr'),
				title = tr.data('item-title'),
				dtitle = tr.data('item-dtitle'),
				unit_manager = tr.data('item-manager'),
				hrid = tr.data('hrid'),
                item_id = tr.data('item-id');

			$("#unit-name-edit").val(title);
			$("#unit-manager-edit").val(unit_manager).trigger('change');
			$("#unit-id-edit").val(item_id);
			$("#unit-row-id").val(hrid);
			$("#unit-dept-title-edit").val(dtitle).trigger('change');
		});

		$(document).on('click', '#update-unit-btn', function(e){

			e.preventDefault();

			var btn = $(this),
				btn_text = btn.html(),
				unit_name = $("#unit-name-edit").val(),
				unit_id = $("#unit-id-edit").val(),
				unit_manager = $("#unit-manager-edit").val(),
				dept_title = $("#unit-dept-title-edit").val(),
				load_element = "#urow-" + $("#unit-row-id").val(),
				token ='{{ Session::token() }}',
				url = "{{route('portal.depts.update.unit')}}";

			$.ajax({
				type: "POST",
				url: url,
				data: {
					unit_name: unit_name,
					dept_title: dept_title,
					unit_manager: unit_manager,
					unit_id: unit_id,
					_token: token
				},
				beforeSend: function () {
					btn.html('<i class="fa fa-spinner fa-spin"></i>');
				},
				success: function(response) {
					btn.html(btn_text);
					swal_alert('Unit Updated', '', 'success', 'continue');
					$('#edit-unit-modal').modal('hide');
					$(load_element).data('item-title',unit_name);
					$(load_element).data('item-dtitle',dept_title);
					$(load_element).data('item-manager',unit_manager);
					$(load_element).load(location.href + " "+ load_element +">*","");
				},
				error: function(jqXHR, exception){
					btn.html(btn_text);
					var error = getErrorMessage(jqXHR, exception);
					swal_alert('Failed to Update Unit',error,'error','Go Back');
				}
			});
		});

		$('#delete-unit-modal').on('show.bs.modal', function (e) {
			var btn = $(e.relatedTarget),
				tr = btn.closest('tr'),
				delete_unit_title = tr.data('item-title'),
				hrid = tr.data('hrid'),
				item_id = tr.data('item-id');

			$("#delete-unit-title").text(delete_unit_title);
			$("#unit-id-delete").val(item_id);
			$("#unit-row-id-delete").val(hrid);
		});

		$(document).on('click', '#delete-unit-btn', function(e){
			e.preventDefault();
			var btn = $(this),
				btn_text = btn.html(),
				item_id = $('#unit-id-delete').val(),

				remove_element = '#urow-' + $("#unit-row-id-delete").val(),
				load_element = '#loadDiv',
				token ='{{ Session::token() }}',
				url = "{{route('portal.depts.delete.unit')}}";

			$.ajax({
				type: "POST",
				url: url,
				data: {
					item_id: item_id,
					_token: token
				},
				beforeSend: function () {
					btn.html('<i class="fa fa-spinner fa-spin"></i>');
				},
				success: function(response) {
					btn.html(btn_text);
					$('#delete-unit-modal').modal('hide');
					swal_alert('Unit Deleted', '', 'success', 'continue');
					$(remove_element).remove();
					//$(load_element).load(location.href + " "+ load_element +">*","");
				},
				error: function(jqXHR, exception){
					btn.html(btn_text);
					var error = getErrorMessage(jqXHR, exception);
					swal_alert('Failed to Delete Unit',error,'error','Go Back');
				}
			});
		});
    });

</script>

@endsection
