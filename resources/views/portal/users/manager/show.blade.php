@extends('layouts.portal')
@section('page_title','Manager: '.$manager->firstname.' '.$manager->lastname.' - ')
@section('portal_page_title') <i class="fas fa-user-tie mr-3"></i>Manager: {{$manager->firstname.' '.$manager->lastname}} @endSection

@section('bc')
    <nav aria-label="breadcrumb" class="d-none d-md-block">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('portal')}}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{route('portal.users')}}">Users</a></li>
            <li class="breadcrumb-item"><a href="{{route('managers.index')}}">Managers</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $manager->firstname.' '.$manager->lastname }}</li>
        </ol>
    </nav>
@endSection

@section('content')

<div class="row">
    <div class="col-sm-3">
        @include('partials.portal.profile',['userdata' => $manager])
        <div class="card">
            <div class="card-body">
                <p class="text-center mb-0">{{$manager->users->count()}} Subordinates.</p>
            </div>
        </div>
    </div>

    <div class="col-sm-9">

        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="card-title mb-0">Subordinates</h5>
            </div>
            <div class="card-body">
                @if($manager->users->count() > 0)
                    @foreach($manager->users as $u)
                        <button class="btn btn-info btn-sm mr-1 mb-1" disabled>{{$u->user->firstname.' '.$u->user->lastname}}</button>
                    @endforeach
                @else
                    <p class="text-muted">This manager does not have subordinates</p>
                @endif
            </div>
            <div class="card-footer">
                <button class="btn btn-primary btn-sm" title="Edit {{ $manager->firstname.' '.$manager->lastname }} subordinates" data-toggle="modal" data-target="#edit-modal"><i class="fas fa-pencil-alt mr-2"></i>Edit</button>
            </div>
        </div>
    </div>
</div>

@endsection






@section('page_footer')

@if(Laratrust::can('update-manager'))
<div class="modal fade" id="edit-modal" tabinndex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog w500" role="document">
		<div class="modal-content">
			<form method="post">

				<div class="modal-header">
                    <h5 class="modal-title">Update <span class="text-primary">{{$manager->firstname.' '.$manager->lastname}}</span> Subordinates</h5>
				</div>

				<div class="modal-body">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="user-unit">
                        <label class="form-check-label font-weight-normal" for="user-unit">Update Staff Unit?</label>
                    </div>

                    <div class="form-group mt-3">
                        <select name="users[]" id="users" class="form-control select" multiple="multiple" style="width: 100%;">
                            <?php
                            $x = array();
                            foreach($manager->users as $u)
                            {
                                array_push($x,$u->user->email);
                            }
                            ?>
                            @foreach($users as $user)
                                <option value="{{$user->email}}" @if(in_array($user->email,$x)) selected @endif>{{$user->firstname.' '.$user->lastname}}</option>
                            @endforeach
                        </select>
                    </div>
				</div>

				<div class="modal-footer">
                    <button type="button" class="btn-default btn" data-dismiss="modal" aria-label="Close"><i class="fas fa-times mr-2"></i>Cancel</button>
                    <button class="btn-success btn" id='edit-btn' type="submit" role="button"><i class="fas fa-check mr-2"></i>Update</button>
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

        // $('#edit-modal').modal('show');

        $(document).on('click', '#edit-btn', function(e){

			e.preventDefault();

            var btn = $(this),
				btn_text = btn.html(),
				users = $("#users").val(),
                user_unit = $("#user-unit").is(':checked'),
				token ='{{ Session::token() }}',
				url = "{{route('managers.update', ':id')}}";
                url = url.replace(':id',"{{Crypt::encrypt($manager->id)}}");

			$.ajax({
				type: "PUT",
				url: url,
				data: {
					user_unit: user_unit,
					users: users,
					_token: token
				},
				beforeSend: function () {
					btn.html('<i class="fas fa-spinner fa-spin"></i>');
				},
				success: function(response) {
					btn.html(btn_text);
                    $('#edit-modal').modal('hide');
					swal_alert('Manager subordinates updated','','success','Continue');
                    window.setTimeout(function(){
                        window.location.href = "{{route('managers.show',Crypt::encrypt($manager->id))}}";
                    },1000);
				},
				error: function(jqXHR, exception){
					btn.html(btn_text);
					var error = getErrorMessage(jqXHR, exception);
                    swal_alert('Failed to update Manager subordinates',error,'error','Go Back');
				}
			});
        });

    });

</script>

@endsection
