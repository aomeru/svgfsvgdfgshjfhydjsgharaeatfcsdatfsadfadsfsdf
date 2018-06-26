@extends('layouts.portal')
@section('page_title','Managers - ')
@section('portal_page_title') <i class="fas fa-user-tie mr-3"></i>Managers @endSection

@section('bc')
    <nav aria-label="breadcrumb" class="d-none d-md-block">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('portal')}}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{route('portal.users')}}">Users</a></li>
            <li class="breadcrumb-item active" aria-current="page">Managers</li>
        </ol>
    </nav>
@endSection

@section('content')

<div class="card">
    <div class="card-body">
        @if(Laratrust::can('create-manager'))
        <div class="mb-3 d-flex justify-content-end">
            <button class="btn btn-primary btn-sm no-margin" title="Create new manager" data-toggle="modal" data-target="#add-modal"><i class="fas fa-plus"></i></button>
        </div>
        @endif

        @if ($list->count() == 0)
            <div class="alert alert-info" role="role">No manager record found.</div>
        @else

            <div class="table-responssive">

                <table class="table table-striped table-bordered table-hover nowwrap data-table" width="100%" data-page-length="25">

                    <thead>
                        <tr class="active">
                            <th>#</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th class="text-center">Staffs</th>
                            @if(Laratrust::can('read-manager|delete-manager'))
                                <th class="text-right">Actions</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>

                        @php $row_count = 1 @endphp

                        @foreach($list as $item)

                            <tr id="row-{{$item->id}}" data-hrid="{{$item->id}}" data-id="{{Crypt::encrypt($item->id)}}" data-title="{{ $item->firstname.' '.$item->lastname }}">

                                <td>{{ $row_count }}</td>

                                <td>{{ $item->firstname.' '.$item->lastname }}</td>

                                <td>{!! $item->unit ? $item->unit->title.' / '.$item->unit->department->title : '<em class="text-muted">N/A</em>' !!}</td>

                                <td class="text-center">{{ $item->users->count() }}</td>

                                @if(Laratrust::can('read-manager|delete-manager'))
                                <td class="text-right">
                                    @if(Laratrust::can('read-manager'))<a href="{{route('managers.show',Crypt::encrypt($item->id))}}" class="btn btn-light btn-sm" title="View {{ $item->firstname.' '.$item->lastname }}"><i class="far fa-eye"></i></a>@endif

                                    @if(Laratrust::can('delete-manager'))<button class="btn btn-danger btn-sm" title="Delete {{ $item->firstname.' '.$item->lastname }}" data-toggle="modal" data-target="#delete-modal"><i class="far fa-trash-alt"></i></button>@endif
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

@if(Laratrust::can('create-manager'))
<div class="modal fade" id="add-modal" tabinndex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog sm-w500" role="document">
		<div class="modal-content">
            <form method="post">

				<div class="modal-header"><h5 class="modal-title">Create Manager</h5></div>

				<div class="modal-body">

                    <div class="form-group">
                        <label for="manager" class="form-control-label sr-onlyy">Manager</label>
                        <select name="manager" id="manager" class="form-control select" style="width: 100%;">
                            <option>Select Manager</option>
                            @foreach($musers as $muser)
                                <option value="{{$muser->email}}">
                                    {{$muser->firstname.' '.$muser->lastname}}
                                    @if($muser->unit != null)
                                        {{ ' - '.$muser->unit->title}}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <small class="my-3 help text-muted">Please use the view button on the list to update employees where manager name was not found in the list above.</small>
                    </div>

                    <div class="form-group">
                        <label for="unit" class="form-control-label sr-onlyy">Unit</label>
                        <select name="unit" id="unit" class="form-control select" style="width: 100%;">
                            <option>Select Unit</option>
                            @foreach($units as $unit)
                                <option value="{{$unit->title}}">{{ $unit->title.' - '.$unit->department->title}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="unit-manager" checked>
                        <label class="form-check-label font-weight-normal" for="unit-manager">Make Unit Manager?</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="user-unit">
                        <label class="form-check-label font-weight-normal" for="user-unit">Update Staff Unit?</label>
                    </div>

                    <div class="form-check">
                        <label class="form-check-label font-weight-normal" for="exampleRadios1">
                            <input class="form-check-input" type="radio" name="unit_or_users" id="or-unit" value="or-unit">
                            Make Manager for staffs in selected unit
                        </label>
                    </div>

                    <div class="form-check">
                        <label class="form-check-label font-weight-normal" for="exampleRadios2">
                            <input class="form-check-input" type="radio" name="unit_or_users" id="or-users" value="or-users" checked>
                            Make Manager for selected staff
                        </label>
                    </div>

                    <div id="show-staffs" class="form-group mt-3">
                        <label for="staffs" class="form-control-label sr-onlyy">Select Staff</label>
                        <select name="staffs[]" id="staffs" class="form-control select" multiple style="width: 100%;">
                            <option>Select Users</option>
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

				</div>

				<div class="modal-footer">
                    <input id="permmode" type="hidden" readonly value="basic">
                    <button type="button" class="btn-default btn" data-dismiss="modal" aria-label="Close"><i class="fas fa-times mr-2"></i>Cancel</button>
                    <button class="btn-primary btn" id='add-btn' type="submit" role="button"><i class="fas fa-check mr-2"></i>Create</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endif

@if(Laratrust::can('delete-manager'))
<div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog w300 sm-w500" role="document">
		<div class="modal-content">
            <div class="modal-body">
                <p class="text-center font-18x no-bottom-margin">Are you sure you want to delete "<span id="delete-title" class="c-06f"></span>" manager record?</p>
            </div>

            <div class="modal-footer mh-override">
                <input type="hidden" id="manager-row-id-delete">
                <input type="hidden" id="manager-id-delete">
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

        // $('#add-modal').modal('show');

        // console.log($("#user-unit").is(':checked'));

        function switchstaff(x)
        {
            var elm = $('#show-staffs'), s = $('#staffs');
            if(x == 'or-users')
            {
                s.prop('disabled',false);
                elm.removeClass('d-none');
            } else {
                s.prop('disabled',true);
                elm.addClass('d-none');
            }
        }
        switchstaff($('input[name=unit_or_users]:checked').val());
        $('input[name=unit_or_users]').on('change', function(){
            switchstaff($(this).val());
        });

        @if(Laratrust::can('create-manager'))
        $(document).on('click', '#add-btn', function(e){

			e.preventDefault();

			var btn = $(this),
				btn_text = btn.html(),
				manager = $("#manager").val(),
				unit = $("#unit").val(),
				unit_manager = $("#unit-manager").is(':checked'),
				user_unit = $("#user-unit").is(':checked'),
				unit_users = $('input[name=unit_or_users]:checked').val(),
				staffs = $("#staffs").val(),
				token ='{{ Session::token() }}',
				url = "{{route('managers.store')}}";

			$.ajax({
				type: "POST",
				url: url,
				data: {
					manager: manager,
					unit: unit,
					unit_manager: unit_manager,
					unit_users: unit_users,
					staffs: staffs,
					_token: token
				},
				beforeSend: function () {
					btn.html('<i class="fas fa-spinner fa-spin"></i>');
				},
				success: function(response) {
					btn.html(btn_text);
                    $('#add-modal').modal('hide');
					swal_alert('Manager Created','','success','Continue');
                    window.setTimeout(function(){
                        window.location.href = "{{route('managers.index')}}";
                    },1000);
				},
				error: function(jqXHR, exception){
					btn.html(btn_text);
					var error = getErrorMessage(jqXHR, exception);
                    swal_alert('Failed to create Manager',error,'error','Go Back');
				}
			});
        });
        @endif

        @if(Laratrust::can('delete-manager'))
        $('#delete-modal').on('show.bs.modal', function (e) {
			var btn = $(e.relatedTarget),
				tr = btn.closest('tr'),
                delete_title = tr.data('title'),
				hrid = tr.data('hrid'),
				item_id = tr.data('id');

			$("#delete-title").text(delete_title);
			$("#manager-id-delete").val(item_id);
			$("#manager-row-id-delete").val(hrid);
		});

		$(document).on('click', '#delete-btn', function(e){
			e.preventDefault();
			var btn = $(this),
				btn_text = btn.html(),
				item_id = $('#manager-id-delete').val(),
				remove_element = '#row-' + $("#manager-row-id-delete").val(),
				load_element = '#loadDiv',
				token ='{{ Session::token() }}',
				url = "{{route('managers.destroy', ':id')}}";
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
                    swal_alert('Manager record deleted','','success','Continue');
					$(remove_element).remove();
					//$(load_element).load(location.href + " "+ load_element +">*","");
				},
				error: function(jqXHR, exception){
					btn.html(btn_text);
					var error = getErrorMessage(jqXHR, exception);
					swal_alert('Failed to delete manager record',error,'error','Go Back');
				}
			});
        });
        @endif

    });

</script>

@endsection
