@extends('layouts.portal')
@section('page_title','Roles: '.$role->display_name.' - ')
@section('portal_page_title') <i class="fas fa-user-shield mr-3"></i>Role: {{$role->display_name}} @endSection

@section('bc')
    <nav aria-label="breadcrumb" class="d-none d-md-block">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('portal')}}">Dashboard</a></li>
            <li class="breadcrumb-item">Settings</li>
            <li class="breadcrumb-item"><a href="{{route('roles.index')}}">Roles</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{$role->display_name}}</li>
        </ol>
    </nav>
@endSection

@section('content')

<div class="row">
    <div class="col-sm-3">
        <div class="card">
            <div class="card-header bg-dark">
                <h5 class="card-title mb-0 text-white">Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3"><div class="col-5"><strong>Name</strong></div><div class="col-7">{{$role->name}}</div></div>
                <div class="row mb-3"><div class="col-5"><strong>Display Name</strong></div><div class="col-7">{{$role->display_name}}</div></div>
                <p class="mb-3">
                    <strong>Description</strong><br>
                    <span id="role-descrip">{!!$role->description == null ? '<span class="c-999">No Description</span>' : $role->description!!}</span>
                </p>
                <div class="row mb-3"><div class="col-5"><strong>Users</strong></div><div class="col-7">{{$role->users->count()}}</div></div>
                <div class="row"><div class="col-5"><strong>Permissions</strong></div><div class="col-7">{{$role->permissions->count()}}</div></div>
            </div>
            @if(Laratrust::can('update-role'))
            <div class="card-footer">
                <button class="btn btn-primary btn-sm" title="Edit {{ $role->display_name }} description" data-toggle="modal" data-target="#edit-role-modal"><i class="fas fa-pencil-alt mr-2"></i>Edit</button>
            </div>
            @endif
        </div>
    </div>
    <div class="col-sm-9">

        <div class="card text-center">
            <div class="card-header bg-dark text-white">
                <ul class="nav nav-tabs card-header-tabs" id="{{$role->name}}-tabs" role="tablist">
                    <li class="nav-item">
                        <h5 class="nav-link active card-title mb-0" id="{{$role->name}}-permissions-tab" data-toggle="tab" role="tab" href="#{{$role->name}}-permissions" aria-controls="{{$role->name}}-permissions" aria-selected="true" style="cursor: pointer">Permissions</h5>
                    </li>
                    <li class="nav-item">
                        <h5 class="nav-link card-title mb-0" id="{{$role->name}}-users-tab" data-toggle="tab" role="tab" href="#{{$role->name}}-users" aria-controls="{{$role->name}}-users" aria-selected="false" style="cursor: pointer">User Assignments</h5>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active text-left" id="{{$role->name}}-permissions" role="tabpanel" aria-labelledby="{{$role->name}}-permissions-tab">
                        <div class="d-flex justify-content-between">
                            <h5>{{$role->display_name}} Permissions</h5>
                            @if(Laratrust::can('assign-remove-permission'))
                            <div class="text-right">
                                <button class="btn btn-primary btn-sm" title="Edit {{ $role->display_name }} permissions" data-toggle="modal" data-target="#perm-modal"><i class="fas fa-pencil-alt mr-2"></i>Edit</button>
                            </div>
                            @endif
                        </div>

                        <hr class="my-3">
                        @if($role->permissions->count() > 0)
                            @foreach($role->permissions as $p)
                                <button class="btn btn-info btn-sm mr-1 mb-1" disabled>{{$p->name}}</button>
                            @endforeach
                        @else
                            <p class="text-muted">No permission assigned to this role yet</p>
                        @endif
                    </div>
                    <div class="tab-pane fade text-left" id="{{$role->name}}-users" role="tabpanel" aria-labelledby="{{$role->name}}-users-tab">
                        <div class="d-flex justify-content-between">
                            <h5>{{$role->display_name}} Users</h5>
                            @if(Laratrust::can('assign-remove-role'))
                            <div class="text-right">
                                <button class="btn btn-primary btn-sm" title="Edit {{ $role->display_name }} users" data-toggle="modal" data-target="#users-modal"><i class="fas fa-pencil-alt mr-2"></i>Edit</button>
                                <a href="{{route('roles.tousers', $role->name)}}" class="btn btn-secondary btn-sm ml-1" title="Assign {{ $role->display_name }} to all users"><i class="fas fa-user-plus mr-2"></i>To all Users</a>
                                <a href="{{route('roles.fromusers', $role->name)}}" class="btn btn-secondary btn-sm ml-1" title="Remove {{ $role->display_name }} from all users"><i class="fas fa-user-minus mr-2"></i>From all Users</a>
                            </div>
                            @endif
                        </div>

                        <hr class="my-3">
                        @if($role->users->count() > 0)
                            @foreach($role->users as $u)
                                <button class="btn btn-info btn-sm mr-1 mb-1" disabled>{{$u->firstname.' '.$u->lastname}}</button>
                            @endforeach
                        @else
                        <p class="text-muted">No user assigned to this role yet</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection






@section('page_footer')

@if(Laratrust::can('assign-remove-role'))
<div class="modal fade" id="users-modal" tabinndex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg w5000" role="document">
		<div class="modal-content">
			<form method="post">

				<div class="modal-header">
                    <h5 class="modal-title">Update <span class="text-primary">{{$role->display_name}}</span> Role Users</h5>
				</div>

				<div class="modal-body">

                    <div class="form-group">
                        <select name="role_users[]" id="role-users" class="form-control select" multiple="multiple" style="width: 100%;">
                            <?php
                            $olist = $role->users()->pluck('email')->toArray();
                            ?>
                            @foreach($users as $user)
                                <option value="{{$user->email}}" @if(in_array($user->email,$olist)) selected @endif>{{$user->firstname.' '.$user->lastname}}</option>
                            @endforeach
                        </select>
                    </div>

				</div>

				<div class="modal-footer">
                    <button type="button" class="btn-default btn" data-dismiss="modal" aria-label="Close"><i class="fas fa-times mr-2"></i>Cancel</button>
                    <button class="btn-success btn" id='role-users-btn' type="submit" role="button"><i class="fas fa-check mr-2"></i>Update</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endif

@if(Laratrust::can('assign-remove-permission'))
<div class="modal fade" id="perm-modal" tabinndex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog w500" role="document">
		<div class="modal-content">
			<form method="post">

				<div class="modal-header">
                    <h5 class="modal-title">Update <span class="text-primary">{{$role->display_name}}</span> Permissions</h5>
				</div>

				<div class="modal-body">

                    <div class="form-group">
                        <select name="role_perm[]" id="role-perm" class="form-control select" multiple="multiple" style="width: 100%;">
                            <?php
                            $olist = $role->permissions()->pluck('name')->toArray();
                            ?>
                            @foreach($permissions as $p)
                                <option value="{{$p->name}}" @if(in_array($p->name,$olist)) selected @endif>{{$p->display_name}}</option>
                            @endforeach
                        </select>
                    </div>

				</div>

				<div class="modal-footer">
                    <button type="button" class="btn-default btn" data-dismiss="modal" aria-label="Close"><i class="fas fa-times mr-2"></i>Cancel</button>
                    <button class="btn-success btn" id='role-perm-btn' type="submit" role="button"><i class="fas fa-check mr-2"></i>Update</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endif

@if(Laratrust::can('update-role'))
<div class="modal fade" id="edit-role-modal" tabinndex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog w500" role="document">
		<div class="modal-content">
			<form method="post">

				<div class="modal-header">
                    <h5 class="modal-title">Update <span class="text-primary">{{$role->display_name}}</span> Role</h5>
				</div>

				<div class="modal-body">
                    <div class="form-group">
                        <label for="descrip" class="form-control-label sr-onlyy">Description</label>

                        <input type="text" id="descrip" class="form-control" placeholder="Enter role description" data-validation="custom required" data-validation-regexp="^([a-zA-Z0-9- ]*)$" data-validation-error-msg="Please use aplhanumeric characters only and hypen" value="{{$role->description}}">
                    </div>
				</div>

				<div class="modal-footer">
                    <button type="button" class="btn-default btn" data-dismiss="modal" aria-label="Close"><i class="fas fa-times mr-2"></i>Cancel</button>
                    <button class="btn-success btn" id='edit-role-btn' type="submit" role="button"><i class="fas fa-check mr-2"></i>Update</button>
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

        @if(Laratrust::can('assign-remove-role'))
        $(document).on('click', '#role-users-btn', function(e){

			e.preventDefault();

            var btn = $(this),
				btn_text = btn.html(),
				role_users = $("#role-users").val(),
				token ='{{ Session::token() }}',
				url = "{{route('roles.update', ':id')}}";
                url = url.replace(':id',"{{$role->name}}");

			$.ajax({
				type: "PUT",
				url: url,
				data: {
					role_users: role_users,
					umode: 'users',
					_token: token
				},
				beforeSend: function () {
					btn.html('<i class="fas fa-spinner fa-spin"></i>');
				},
				success: function(response) {
					btn.html(btn_text);
                    $('#users-modal').modal('hide');
					swal_alert('Role Users updated','','success','Continue');
                    window.setTimeout(function(){
                        window.location.href = "{{route('roles.show',Crypt::encrypt($role->id))}}";
                    },1000);
				},
				error: function(jqXHR, exception){
					btn.html(btn_text);
					var error = getErrorMessage(jqXHR, exception);
                    swal_alert('Failed to update Role Users',error,'error','Go Back');
				}
			});
        });
        @endif

        @if(Laratrust::can('assign-remove-permission'))
        $(document).on('click', '#role-perm-btn', function(e){

			e.preventDefault();

            var btn = $(this),
				btn_text = btn.html(),
				role_perm = $("#role-perm").val(),
				token ='{{ Session::token() }}',
				url = "{{route('roles.update', ':id')}}";
                url = url.replace(':id',"{{$role->name}}");

			$.ajax({
				type: "PUT",
				url: url,
				data: {
					role_perm: role_perm,
                    umode: 'perms',
					_token: token
				},
				beforeSend: function () {
					btn.html('<i class="fas fa-spinner fa-spin"></i>');
				},
				success: function(response) {
					btn.html(btn_text);
                    $('#perm-modal').modal('hide');
					swal_alert('Role Permissions updated','','success','Continue');
                    window.setTimeout(function(){
                        window.location.href = "{{route('roles.show',Crypt::encrypt($role->id))}}";
                    },1000);
				},
				error: function(jqXHR, exception){
					btn.html(btn_text);
					var error = getErrorMessage(jqXHR, exception);
                    swal_alert('Failed to update Role Permissions',error,'error','Go Back');
				}
			});
        });
        @endif

        @if(Laratrust::can('update-role'))
        $(document).on('click', '#edit-role-btn', function(e){

			e.preventDefault();

            var btn = $(this),
				btn_text = btn.html(),
				description = $("#descrip").val(),
				token ='{{ Session::token() }}',
                load_element = "#role-descrip",
				url = "{{route('roles.ed', ':id')}}";
                url = url.replace(':id',"{{$role->name}}");

			$.ajax({
				type: "PUT",
				url: url,
				data: {
					description: description,
					_token: token
				},
				beforeSend: function () {
					btn.html('<i class="fas fa-spinner fa-spin"></i>');
				},
				success: function(response) {
					btn.html(btn_text);
                    $('#edit-role-modal').modal('hide');
					swal_alert('Role description updated','','success','Continue');
                    $('#role-descrip').text(description);
                    // $(load_element).load(location.href + " "+ load_element +">*","");
                    // window.setTimeout(function(){
                    //     window.location.href = "{{route('roles.show',Crypt::encrypt($role->id))}}";
                    // },1000);
				},
				error: function(jqXHR, exception){
					btn.html(btn_text);
					var error = getErrorMessage(jqXHR, exception);
                    swal_alert('Failed to update Role description',error,'error','Go Back');
				}
			});
        });
        @endif
    });

</script>

@endsection
