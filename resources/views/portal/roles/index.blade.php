@extends('layouts.portal')
@section('page_title','Roles - ')
@section('portal_page_title') <i class="fas fa-user-shield mr-3"></i>Roles @endSection

@section('bc')
    <nav aria-label="breadcrumb" class="d-none d-md-block">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('portal')}}">Dashboard</a></li>
            <li class="breadcrumb-item">Settings</li>
            <li class="breadcrumb-item active" aria-current="page">Roles</li>
        </ol>
    </nav>
@endSection

@section('content')

<div class="card">
    <div class="card-body">
        @if(Laratrust::can('create-role'))
        <div class="mb-3 d-flex justify-content-end">
            <button class="btn btn-primary btn-sm no-margin" title="Add new role" data-toggle="modal" data-target="#add-modal"><i class="fas fa-plus"></i></button>
        </div>
        @endif

        @if ($roles->count() == 0)
            <div class="alert alert-info" role="role">No role record found.</div>
        @else

            <div class="table-responssive">

                <table class="table table-striped table-bordered table-hover nowwrap data-table" width="100%" data-page-length="25">

                    <thead>
                        <tr class="active">
                            <th>#</th>
                            <th>Display Name</th>
                            <th>Slug</th>
                            <th>Description</th>
                            <th class="text-center">Users</th>
                            <th class="text-center">Permissions</th>
                            @if(Laratrust::can('read-role|delete-role'))<th class="text-right">Actions</th>@endif
                        </tr>
                    </thead>

                    <tbody>

                        @php $row_count = 1 @endphp

                        @foreach($roles as $item)

                            <tr id="row-{{$item->id}}" data-hrid="{{$item->id}}" data-id="{{Crypt::encrypt($item->id)}}" data-title="{{$item->display_name}}">

                                <td>{{ $row_count }}</td>

                                <td>{{ $item->display_name }}</td>

                                <td>{{ $item->name }}</td>

                                <td>{{ $item->description }}</td>

                                <td class="text-center">{!! $item->users->count() == 0 ? '<span class="c-999">0</span>' : $item->users->count() !!}</td>

                                <td class="text-center">{!! $item->permissions->count() == 0 ? '<span class="c-999">0</span>' : $item->permissions->count() !!}</td>

                                @if(Laratrust::can('read-role|delete-role'))
                                <td class="text-right">
                                    @if(Laratrust::can('read-role'))<a href="{{route('roles.show',Crypt::encrypt($item->id))}}" class="btn btn-light btn-sm" title="View {{ $item->display_name }}"><i class="far fa-eye"></i></a>@endif

                                    @if(Laratrust::can('delete-role'))<button class="btn btn-danger btn-sm" title="Delete {{ $item->display_name }}" data-toggle="modal" data-target="#delete-modal"><i class="far fa-trash-alt"></i></button>@endif
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

@if(Laratrust::can('create-role'))
<div class="modal fade" id="add-modal" tabinndex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog w300 sm-w500" role="document">
		<div class="modal-content">
			<form method="post">

				<div class="modal-header">
					<h5 class="modal-title">Create Role</h5>
				</div>

				<div class="modal-body">
					<div class="form-group">
                        <label for="display-name" class="form-control-label sr-onlyy">Role Title</label>

                        <input type="text" id="display-name" class="form-control" placeholder="Please enter role display Name" data-validation="custom required" data-validation-regexp="^([a-zA-Z0-9- ]*)$" data-validation-error-msg="Please use aplhanumeric characters only and hypen" maxlength="100">

                        <p class="mt-3 text-secondary">Unique Slug: <span id="slug" class="text-info"></span></p>
                        <input id="slugh" hidden readonly type="text">
                    </div>


                    <div class="form-group">
                        <label for="descrip" class="form-control-label sr-onlyy">Description</label>

                        <input type="text" id="descrip" class="form-control" placeholder="Enter role description" data-validation="custom required" data-validation-regexp="^([a-zA-Z0-9- ]*)$" data-validation-error-msg="Please use aplhanumeric characters only and hypen">
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

@if(Laratrust::can('delete-role'))
<div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog w300 sm-w500" role="document">
		<div class="modal-content">
            <div class="modal-body">
                <p class="text-center font-18x no-bottom-margin">Are you sure you want to delete "<span id="delete-title" class="c-06f"></span>" role?</p>
            </div>

            <div class="modal-footer mh-override">
                <input type="hidden" id="role-row-id-delete">
                <input type="hidden" id="role-id-delete">
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

        if($('#display-name').val().length >= 3)
        {
            $('#slug').text(get_slug($('#display-name').val()));
            $('#slugh').val(get_slug($('#display-name').val()));
        }

		$('#display-name').on('keyup', function () {
            if($(this).val().length >= 3)
            {
                $('#slug').text(get_slug($(this).val()));
                $('#slugh').val(get_slug($(this).val()));
            }
		}).on('focusout', function () {
            if($(this).val().length >= 3)
            {
                $('#slug').text(get_slug($(this).val()));
                $('#slugh').val(get_slug($(this).val()));
            }
        });
        
        @if(Laratrust::can('create-role'))
        $(document).on('click', '#add-btn', function(e){

			e.preventDefault();

			var btn = $(this),
				btn_text = btn.html(),
				display_name = $("#display-name").val(),
				descrip = $("#descrip").val(),
				name = $("#slugh").val(),
				token ='{{ Session::token() }}',
				url = "{{route('roles.store')}}";

			$.ajax({
				type: "POST",
				url: url,
				data: {
					display_name: display_name,
					description: descrip,
					name: name,
					_token: token
				},
				beforeSend: function () {
					btn.html('<i class="fas fa-spinner fa-spin"></i>');
				},
				success: function(response) {
					btn.html(btn_text);
                    $('#add-modal').modal('hide');
					swal_alert('Role Created','','success','Continue');
                    window.setTimeout(function(){
                        window.location.href = "{{route('roles.index')}}";
                    },1000);
				},
				error: function(jqXHR, exception){
					btn.html(btn_text);
					var error = getErrorMessage(jqXHR, exception);
                    swal_alert('Failed to Create Department',error,'error','Go Back');
				}
			});
        });
        @endif

        @if(Laratrust::can('delete-role'))
        $('#delete-modal').on('show.bs.modal', function (e) {
			var btn = $(e.relatedTarget),
				tr = btn.closest('tr'),
                delete_title = tr.data('title'),
				hrid = tr.data('hrid'),
				item_id = tr.data('id');

			$("#delete-title").text(delete_title);
			$("#role-id-delete").val(item_id);
			$("#role-row-id-delete").val(hrid);
		});

		$(document).on('click', '#delete-btn', function(e){
			e.preventDefault();
			var btn = $(this),
				btn_text = btn.html(),
				item_id = $('#role-id-delete').val(),

				remove_element = '#row-' + $("#role-row-id-delete").val(),
				load_element = '#loadDiv',
				token ='{{ Session::token() }}',
				url = "{{route('roles.destroy', ':id')}}";
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
                    swal_alert('Role Deleted','','success','Continue');
					$(remove_element).remove();
					//$(load_element).load(location.href + " "+ load_element +">*","");
				},
				error: function(jqXHR, exception){
					btn.html(btn_text);
					var error = getErrorMessage(jqXHR, exception);
					swal_alert('Failed to Delete Role',error,'error','Go Back');
				}
			});
        });
        @endif
    });

</script>

@endsection
