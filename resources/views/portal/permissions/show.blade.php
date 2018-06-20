@extends('layouts.portal')
@section('page_title','Permission: '.$perm->display_name.' - ')
@section('portal_page_title') <i class="fas fa-lock mr-3"></i>Permission: {{$perm->display_name}} @endSection

@section('bc')
    <nav aria-label="breadcrumb" class="d-none d-md-block">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('portal')}}">Dashboard</a></li>
            <li class="breadcrumb-item">Settings</li>
            <li class="breadcrumb-item"><a href="{{route('permissions.index')}}">Permissions</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{$perm->display_name}}</li>
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
                <div class="row mb-3"><div class="col-5"><strong>Name</strong></div><div class="col-7">{{$perm->name}}</div></div>
                <div class="row mb-3"><div class="col-5"><strong>Display Name</strong></div><div class="col-7">{{$perm->display_name}}</div></div>
                <p class="mb-3">
                    <strong>Description</strong><br>
                    <span id="perm-descrip">{!!$perm->description == null ? '<span class="c-999">No Description</span>' : $perm->description!!}</span>
                </p>
                <div class="row mb-3"><div class="col-5"><strong>Users</strong></div><div class="col-7">{{$perm->users->count()}}</div></div>
                <div class="row"><div class="col-5"><strong>Roles</strong></div><div class="col-7">{{$perm->roles->count()}}</div></div>
            </div>
            <div class="card-footer">
                <button class="btn btn-primary btn-sm" title="Edit {{ $perm->display_name }} description" data-toggle="modal" data-target="#edit-perm-modal"><i class="fas fa-pencil-alt mr-2"></i>Edit</button>
            </div>
        </div>
    </div>
    <div class="col-sm-9">

        <div class="card text-center">
            <div class="card-header bg-dark text-white">
                <ul class="nav nav-tabs card-header-tabs" id="{{$perm->name}}-tabs" role="tablist">
                    <li class="nav-item">
                        <h5 class="nav-link active card-title mb-0" id="{{$perm->name}}-roles-tab" data-toggle="tab" role="tab" href="#{{$perm->name}}-roles" aria-controls="{{$perm->name}}-roles" aria-selected="true" style="cursor: pointer">Roles</h5>
                    </li>
                    <li class="nav-item">
                        <h5 class="nav-link card-title mb-0" id="{{$perm->name}}-users-tab" data-toggle="tab" role="tab" href="#{{$perm->name}}-users" aria-controls="{{$perm->name}}-users" aria-selected="false" style="cursor: pointer">User Assignments</h5>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active text-left" id="{{$perm->name}}-roles" role="tabpanel" aria-labelledby="{{$perm->name}}-roles-tab">
                        <div class="d-flex justify-content-between">
                            <h5>{{$perm->display_name}} Role</h5>
                            <div class="text-right">
                                <button class="btn btn-primary btn-sm" title="Edit {{ $perm->display_name }} roles" data-toggle="modal" data-target="#roles-modal"><i class="fas fa-pencil-alt mr-2"></i>Edit</button>
                                <a href="{{route('permissions.toroles', $perm->name)}}" class="btn btn-secondary btn-sm ml-1" title="Assign {{ $perm->display_name }} to all roles"><i class="fas fa-user-lock mr-2"></i>To all Roles</a>
                                <a href="{{route('permissions.fromroles', $perm->name)}}" class="btn btn-secondary btn-sm ml-1" title="Remove {{ $perm->display_name }} from all roles"><i class="fas fa-user-minus mr-2"></i>From all Roles</a>
                            </div>
                        </div>
                        <hr class="my-3">
                        @if($perm->roles->count() > 0)
                            @foreach($perm->roles as $r)
                                <button class="btn btn-info btn-sm mr-1 mb-1" disabled>{{$r->display_name}}</button>
                            @endforeach
                        @else
                            <p class="text-muted">This permission is not assigned to a role yet</p>
                        @endif
                    </div>
                    <div class="tab-pane fade text-left" id="{{$perm->name}}-users" role="tabpanel" aria-labelledby="{{$perm->name}}-users-tab">
                        <div class="d-flex justify-content-between">
                            <h5>{{$perm->display_name}} Users</h5>
                            <div class="text-right">
                                <button class="btn btn-primary btn-sm" title="Edit {{ $perm->display_name }} users" data-toggle="modal" data-target="#users-modal"><i class="fas fa-pencil-alt mr-2"></i>Edit</button>
                                <a href="{{route('permissions.tousers', $perm->name)}}" class="btn btn-secondary btn-sm ml-1" title="Assign {{ $perm->display_name }} to all users"><i class="fas fa-user-plus mr-2"></i>To all Users</a>
                                <a href="{{route('permissions.fromusers', $perm->name)}}" class="btn btn-secondary btn-sm ml-1" title="Remove {{ $perm->display_name }} from all users"><i class="fas fa-user-minus mr-2"></i>From all Users</a>
                            </div>
                        </div>

                        <hr class="my-3">
                        @if($perm->users->count() > 0)
                            @foreach($perm->users as $u)
                                <button class="btn btn-info btn-sm mr-1 mb-1" disabled>{{$u->firstname.' '.$u->lastname}}</button>
                            @endforeach
                        @else
                        <p class="text-muted">This permission is not assigned to a user yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection






@section('page_footer')

<div class="modal fade" id="users-modal" tabinndex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg w5000" role="document">
		<div class="modal-content">
			<form method="post">

				<div class="modal-header">
                    <h5 class="modal-title">Update <span class="text-primary">{{$perm->display_name}}</span> Permission Users</h5>
				</div>

				<div class="modal-body">

                    <div class="form-group">
                        <select name="perm_users[]" id="perm-users" class="form-control select" multiple="multiple" style="width: 100%;">
                            <?php
                            $olist = $perm->users()->pluck('email')->toArray();
                            ?>
                            @foreach($users as $user)
                                <option value="{{$user->email}}" @if(in_array($user->email,$olist)) selected @endif>{{$user->firstname.' '.$user->lastname}}</option>
                            @endforeach
                        </select>
                    </div>

				</div>

				<div class="modal-footer">
                    <button type="button" class="btn-default btn" data-dismiss="modal" aria-label="Close"><i class="fas fa-times mr-2"></i>Cancel</button>
                    <button class="btn-success btn" id='perm-users-btn' type="submit" role="button"><i class="fas fa-check mr-2"></i>Update</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="roles-modal" tabinndex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog w500" role="document">
		<div class="modal-content">
			<form method="post">

				<div class="modal-header">
                    <h5 class="modal-title">Update <span class="text-primary">{{$perm->display_name}}</span> Roles</h5>
				</div>

				<div class="modal-body">

                    <div class="form-group">
                        <select name="perm_roles[]" id="perm-roles" class="form-control select" multiple="multiple" style="width: 100%;">
                            <?php
                            $olist = $perm->roles()->pluck('name')->toArray();
                            ?>
                            @foreach($roles as $r)
                                <option value="{{$r->name}}" @if(in_array($r->name,$olist)) selected @endif>{{$r->display_name}}</option>
                            @endforeach
                        </select>
                    </div>

				</div>

				<div class="modal-footer">
                    <button type="button" class="btn-default btn" data-dismiss="modal" aria-label="Close"><i class="fas fa-times mr-2"></i>Cancel</button>
                    <button class="btn-success btn" id='perm-roles-btn' type="submit" role="button"><i class="fas fa-check mr-2"></i>Update</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="edit-perm-modal" tabinndex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog w500" role="document">
		<div class="modal-content">
			<form method="post">

				<div class="modal-header">
                    <h5 class="modal-title">Update <span class="text-primary">{{$perm->display_name}}</span> Permission</h5>
				</div>

				<div class="modal-body">
                    <div class="form-group">
                        <label for="descrip" class="form-control-label sr-onlyy">Description</label>

                        <input type="text" id="descrip" class="form-control" placeholder="Enter role description" data-validation="custom required" data-validation-regexp="^([a-zA-Z0-9- ]*)$" data-validation-error-msg="Please use aplhanumeric characters only and hypen" value="{{$perm->description}}">
                    </div>
				</div>

				<div class="modal-footer">
                    <button type="button" class="btn-default btn" data-dismiss="modal" aria-label="Close"><i class="fas fa-times mr-2"></i>Cancel</button>
                    <button class="btn-success btn" id='edit-perm-btn' type="submit" role="button"><i class="fas fa-check mr-2"></i>Update</button>
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


        $(document).on('click', '#perm-users-btn', function(e){

			e.preventDefault();

            var btn = $(this),
				btn_text = btn.html(),
				perm_users = $("#perm-users").val(),
				token ='{{ Session::token() }}',
				url = "{{route('permissions.update', ':id')}}";
                url = url.replace(':id',"{{$perm->name}}");

			$.ajax({
				type: "PUT",
				url: url,
				data: {
					perm_users: perm_users,
					umode: 'users',
					_token: token
				},
				beforeSend: function () {
					btn.html('<i class="fas fa-spinner fa-spin"></i>');
				},
				success: function(response) {
					btn.html(btn_text);
                    $('#users-modal').modal('hide');
					swal_alert('Permission Users updated','','success','Continue');
                    window.setTimeout(function(){
                        window.location.href = "{{route('permissions.show',Crypt::encrypt($perm->id))}}";
                    },1000);
				},
				error: function(jqXHR, exception){
					btn.html(btn_text);
					var error = getErrorMessage(jqXHR, exception);
                    swal_alert('Failed to update Permissions Users',error,'error','Go Back');
				}
			});
        });


        $(document).on('click', '#perm-roles-btn', function(e){

			e.preventDefault();

            var btn = $(this),
				btn_text = btn.html(),
				perm_roles = $("#perm-roles").val(),
				token ='{{ Session::token() }}',
				url = "{{route('permissions.update', ':id')}}";
                url = url.replace(':id',"{{$perm->name}}");

			$.ajax({
				type: "PUT",
				url: url,
				data: {
					perm_roles: perm_roles,
                    umode: 'roles',
					_token: token
				},
				beforeSend: function () {
					btn.html('<i class="fas fa-spinner fa-spin"></i>');
				},
				success: function(response) {
					btn.html(btn_text);
                    $('#roles-modal').modal('hide');
					swal_alert('Permission Roles updated','','success','Continue');
                    window.setTimeout(function(){
                        window.location.href = "{{route('permissions.show',Crypt::encrypt($perm->id))}}";
                    },1000);
				},
				error: function(jqXHR, exception){
					btn.html(btn_text);
					var error = getErrorMessage(jqXHR, exception);
                    swal_alert('Failed to update Permissions Roles ',error,'error','Go Back');
				}
			});
        });


        $(document).on('click', '#edit-perm-btn', function(e){

			e.preventDefault();

            var btn = $(this),
				btn_text = btn.html(),
				description = $("#descrip").val(),
				token ='{{ Session::token() }}',
                load_element = "#perm-descrip",
				url = "{{route('permissions.ed', ':id')}}";
                url = url.replace(':id',"{{$perm->name}}");

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
                    $('#edit-perm-modal').modal('hide');
					swal_alert('Permission description updated','','success','Continue');
                    $('#perm-descrip').text(description);
				},
				error: function(jqXHR, exception){
					btn.html(btn_text);
					var error = getErrorMessage(jqXHR, exception);
                    swal_alert('Failed to update Permission description',error,'error','Go Back');
				}
			});
        });

    });

</script>

@endsection
