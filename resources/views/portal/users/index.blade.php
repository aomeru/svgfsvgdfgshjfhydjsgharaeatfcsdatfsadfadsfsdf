@extends('layouts.portal')
@section('page_title','Users - ')
@section('portal_page_title') <i class="fas fa-user-circle mr-3"></i>All Users @endSection

@section('bc')
    <nav aria-label="breadcrumb" class="d-none d-md-block">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('portal')}}">Dashboard</a></li>
            <li class="breadcrumb-item">Users</li>
            <li class="breadcrumb-item active" aria-current="page">All Users</li>
        </ol>
    </nav>
@endSection

@section('content')

<div class="card">
    <div class="card-body">
        {{-- <div class="mb-3 d-flex justify-content-end">
            <button class="btn btn-primary btn-sm no-margin" title="Add new user" data-toggle="modal" data-target="#add-user-modal"><i class="fa fa-plus"></i></button>
        </div> --}}

        @if ($list->count() == 0)
            <p class="alert alert-info">No user record found.</p>
        @else

            <div class="table-responssive">

                <table class="table table-striped table-bordered table-hover nowwrap data-table" width="100%" data-page-length="25">

                    <thead>
                        <tr class="active">
                            <th>#</th>
                            <th>ID</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Job Title</th>
                            <th>Department</th>
                            <th>Manager</th>
                            <th>Employee Type</th>
                            <th>Employee Since</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @php $row_count = 1 @endphp

                        @foreach($list as $item)

                            <tr id="row-{{$item->id}}" data-hrid="{{$item->id}}" data-id="{{Crypt::encrypt($item->id)}}" data-doh="{{$item->date_of_hire}}" data-emp-type="{{$item->employee_type}}" data-staff-id="{{$item->staff_id}}" data-unit-id="{{$item->unit == null ? '' : $item->unit->title}}" data-manager="{{$item->manager == null ? '' : $item->manager->manager->email}}" data-status="{{$item->status}}">

                                <td>{{ $row_count }}</td>

                                <td>{!! $item->staff_id == null ? '<span class="c-999">N/A</span>' : $item->staff_id !!}</td>

                                <td>{!! $item->firstname == null ? '<span class="c-999">N/A</span>' : $item->firstname.' '.$item->lastname !!}</td>

                                <td><u><a href="{{route('portal.users.show', Crypt::encrypt($item->id))}}" class="c-06f">{{$item->email}}</a></u></td>

                                <td>{{ $item->job_title }}</td>

                                <td>{!! $item->unit == null ? '<span class="c-999">N/A</span>' : $item->unit->department->title.' <span class="c-999 v-padding-5">/</span> '.$item->unit->title !!}</td>

                                <td>{!! $item->manager == null ? '<span class="c-999">N/A</span>' : $item->manager->manager->firstname.' '.$item->manager->manager->lastname !!}</td>

                                <td>{{ $item->employee_type }}</td>

                                <td>{{date('M jS, Y', strtotime($item->date_of_hire))}}</td>

                                <td>{{ $item->status }}</td>

                                <td class="text-right">
                                    <button class="btn btn-primary btn-sm" title="Edit {{ $item->firstname }}" data-toggle="modal" data-target="#edit-user-modal"><i class="fas fa-pencil-alt"></i></button>

                                    {{-- <button class="btn btn-danger btn-sm" title="Delete {{ $item->firstname }}" data-toggle="modal" data-target="#delete-user-modal"><i class="far fa-trash-alt"></i></button> --}}
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

@endsection






@section('page_footer')
<div class="modal fade" id="edit-user-modal" tabinndex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog w400 sm-w600" role="document">
        <div class="modal-content">
            <form method="post">

                <div class="modal-header mh-override">
                    <h5 class="modal-title">Update User Account</h5>
                </div>

                <div class="modal-body">

                    <div class="row mb-4">
                        <div class="col-6">
                            <div class="form-group input_field_sections">
                                <label for="staff-id-edit" class="form-control-label sr-onlyy">Staff ID</label>

                                <input type="text" id="staff-id-edit" class="form-control" placeholder="Enter staff ID" data-validation="custom required" data-validation-regexp="^([a-zA-Z0-9-]*)$" data-validation-error-msg="Please use aplhanumeric characters only and hypen" readonly>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group input_field_sections">
                                <label for="doh-edit" class="form-control-label sr-onlyy">Date of Employement</label>

                                <input type="date" id="doh-edit" class="form-control" placeholder="Enter Employment Date" data-validation="custom required" data-validation-regexp="^([a-zA-Z0-9-]*)$" data-validation-error-msg="Please use aplhanumeric characters only and hypen" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-6">
                            <div class="form-group input_field_sections">
                                <label for="unit-edit" class="form-control-label sr-onlyy">Unit / Department</label> <br>
                                <select id="unit-edit" class="form-control select" style="width: 100%;">
                                    <option value="">Select Unit</option>
                                    @foreach($units as $item)
                                        <option value="{{$item->title}}">{{$item->title}} / {{$item->department->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group input_field_sections">
                                <label for="manager-edit" class="form-control-label sr-onlyy">Manager</label>
                                <select id="manager-edit" class="form-control select" style="width: 100%;">
                                    <option value="">Select Manager</option>
                                    @foreach($list as $user)
                                        <option value="{{$user->email}}">{{$user->firstname.' '.$user->lastname}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-6">
                            <div class="form-group input_field_sections">
                                <label for="emp-type-edit" class="form-control-label sr-onlyy">Employee Type</label> <br>
                                <select id="emp-type-edit" class="form-control select-ns" style="width: 100%;">
                                    <option value="Full Time">Full Time</option>
                                    <option value="Graduate Trainee">Graduate Trainee</option>
                                    <option value="Part Time">Part Time</option>
                                    <option value="Contract">Contract</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group input_field_sections">
                                <label for="status-edit" class="form-control-label sr-onlyy">Status</label> <br>
                                <select id="status-edit" class="form-control select-ns" style="width: 100%;">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="deactivated">Deactivate</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer mh-override">
                    <input type="hidden" id="user-id-edit">
                    <input type="hidden" id="row-id">
                    <button type="button" class="btn-danger btn" data-dismiss="modal" aria-label="Close"><i class="fa fa-times mr5"></i>Cancel</button>
                    <button class="btn-success btn" id='edit-user-btn' type="submit" role="button"><i class="fa fa-check mr5"></i>Update</button>
                </div>
            </form>
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

        $('#edit-user-modal').on('show.bs.modal', function (e) {
			var btn = $(e.relatedTarget),
				tr = btn.closest('tr'),
				staff_id = tr.data('staff-id'),
                doh = tr.data('doh'),
				status = tr.data('status'),
				unit_id = tr.data('unit-id'),
				emp_type = tr.data('emp-type'),
				manager = tr.data('manager'),
				user_id = tr.data('id'),
				hrid = tr.data('hrid');

            $("#staff-id-edit").val(staff_id);
            $("#doh-edit").val(doh).trigger('change');
			$("#status-edit").val(status).trigger('change');
			$("#unit-edit").val(unit_id).trigger('change');
			$("#emp-type-edit").val(emp_type).trigger('change');
			$("#manager-edit").val(manager).trigger('change');

			$("#user-id-edit").val(user_id);
			$("#row-id").val(hrid);
        });

        $(document).on('click', '#edit-user-btn', function(e){

			e.preventDefault();

			var btn = $(this),
				btn_text = btn.html(),
				staff_id = $("#staff-id-edit").val(),
				unit_id = $("#unit-edit").val(),
				status = $("#status-edit").val(),
				manager = $("#manager-edit").val(),
                emp_type = $("#emp-type-edit").val(),
                doh = $("#doh-edit").val(),
				user_id = $("#user-id-edit").val(),
				load_element = "#row-" + $("#row-id").val(),
				token ='{{ Session::token() }}',
				url = "{{route('portal.users.update')}}";

			$.ajax({
				type: "POST",
				url: url,
				data: {
					staff_id: staff_id,
					unit_id: unit_id,
					status: status,
					manager: manager,
					emp_type: emp_type,
					doh: doh,
					user_id: user_id,
					_token: token
				},
				beforeSend: function () {
					btn.html('<i class="fa fa-spinner fa-spin"></i>');
				},
				success: function(response) {
					btn.html(btn_text);
					swal_alert('Employee Record Updated', '', 'success', 'Continue');
					$('#edit-user-modal').modal('hide');
					$(load_element).data('staff-id',staff_id);
					$(load_element).data('status',status);
					$(load_element).data('unit-id',unit_id);
					$(load_element).data('emp-type',emp_type);
					$(load_element).data('manager',manager);
					$(load_element).data('doh',doh);
					$(load_element).load(location.href + " "+ load_element +">*","");
				},
				error: function(jqXHR, exception){
					btn.html(btn_text);
					var error = getErrorMessage(jqXHR, exception);
					swal_alert('Failed to update Employee Record', error, 'error', 'Go Back');
				}
			});
		});
    });

</script>

@endsection
