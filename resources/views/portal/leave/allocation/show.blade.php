@extends('layouts.portal')
@section('page_title','Leave Allocation: '.$user->firstname.' '.$user->lastname.' - ')
@section('portal_page_title') <i class="fas fa-calendar mr-3"></i>Leave Allocation: {{$user->firstname.' '.$user->lastname}} @endSection

@section('bc')
    <nav aria-label="breadcrumb" class="d-none d-md-block">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('portal')}}">Dashboard</a></li>
            <li class="breadcrumb-item">Leave</li>
            <li class="breadcrumb-item"><a href="{{route('leave-allocation.index')}}">Allocation</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{$user->firstname.' '.$user->lastname}}</li>
        </ol>
    </nav>
@endSection

@section('content')

<div class="row">
    <div class="col-sm-3">
        @include('partials.portal.profile',['userdata' => $user, 'border' => 'border border-f5'])
    </div>

    <div class="col-sm-9">

        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="card-title mb-0">Leave Allocations</h5>
            </div>
            <div class="card-body">
                @if($las->count() == 0)
                    <p class="mb-0 text-muted">The are no leave allocations for <strong>{{$user->fullname}}</strong> yet</p>
                @else
                <div class="row">
                    @foreach($las as $key => $la)
                        <?php $key; ?>
                        <div class="col-6 col-sm-4">
                            <div id="card-la-{{$key + 1}}" class="card shadomw-sm mb-3" data-hrid="{{$key + 1}}" data-id="{{Crypt::encrypt($la->id)}}" data-title="{{$la->leave_type->title}}">
                                <?php
                                $color = 'info';
                                $marker = round($la->leave_type->allowed/3);
                                if($la->allowed <= $marker) $color = 'danger'; elseif($la->allowed <= ($marker* 2)) $color = 'warning';
                                ?>
                                <div class="card-header text-white bg-{{$color}} progress-bar-striped">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="card-title ttext-capitalize m-0">{{$la->leave_type->title}}</h5>
                                        <div class="display-4">{{$la->allowed}} {{-- <span class="text-secondary" style="font-size: 18px; vertical-align: top;">/{{round($la->leave_type->allowed)}}</span> --}}</div>
                                    </div>
                                </div>
                                <div class="card-body text-whitee bg-darkk p-3">
                                    <p class="mb-0">
                                        <span class="text-secondary">Leave Type Applications: </span>
                                        <span>
                                            <?php
                                            $acount = $user->leave()->where('leave_type_id',$la->leave_type->id)->where('status','<>','pending')->count();
                                            ?>
                                            {{$acount}}
                                        </span>
                                    </p>
                                    <p class="mb-0">
                                        <span class="text-secondary">Taken: </span>
                                        <span>
                                            <?php
                                            $tcount = $user->leave()->where('leave_type_id',$la->leave_type->id)->whereIn('status', ['called off','completed'])->count();
                                            ?>
                                            {{$tcount}}
                                        </span>
                                    </p>
                                    <p class="mb-0">
                                        <span class="text-secondary">Cancelled: </span>
                                        <span>
                                            <?php
                                            $ccount = $user->leave()->where('leave_type_id',$la->leave_type->id)->whereIn('status', ['cancelled'])->count();
                                            ?>
                                            {{$ccount}}
                                        </span>
                                    </p>
                                    <hr class="mb-3">

                                    @if(Laratrust::can('update-leave-allocation')) <button class="btn btn-primary btn-sm la-edit-footer-btn" title="Edit {{ $user->fullname.' '.$la->leave_type->title }} leave allocation record"><i class="fas fa-pencil-alt"></i></button> @endif

                                    @if(Laratrust::can('delete-leave-allocation') && $acount == 0) <button class="btn btn-danger btn-sm la-delete-btn" title="Delete {{ $user->fullname.' '.$la->leave_type->title }} leave allocation record" data-toggle="modal" data-target="#delete-modal"><i class="fas fa-trash-alt"></i></button> @endif
                                </div>
                                <div id="card-footer-{{$key+1}}" class="card-footer d-none">
                                    <button type="button" class="close la-edit-footer-btn" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                                    <p class="text-muted">Please select the number of days allowed for this employee {{$la->leave_type->title}} leave record</p>

                                    <form action="post">
                                        <div class="input-group input-group-sm">
                                            <select class="custom-select allowed-edit" id="allowed-edit-{{$key + 1}}">
                                                @for($y=1;$y<=60;$y++)
                                                    <option value="{{$y}}" @if(round($la->allowed) == $y) selected @endif>{{$y}}</option>
                                                @endfor
                                            </select>
                                            <div class="input-group-append">
                                                <button class="btn btn-success btn-sm la-edit-btn" type="button"><i class="fas fa-check mr-2"></i>Update</button>
                                            </div>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif
            </div>
            {{-- <div class="card-footer">
                <button class="btn btn-primary btn-sm" title="Edit {{ $user->firstname.' '.$user->lastname }} subordinates" data-toggle="modal" data-target="#edit-modal"><i class="fas fa-pencil-alt mr-2"></i>Edit</button>
            </div> --}}
        </div>
    </div>
</div>

@endsection






@section('page_footer')

@if(Laratrust::can('delete-leave-allocation'))
<div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog w300 sm-w500" role="document">
		<div class="modal-content">
            <div class="modal-body">
                <p class="text-center font-18x no-bottom-margin">Are you sure you want to delete "<span id="delete-title" class="c-06f"></span>" leave allocation for {{$user->fullname}}?</p>
            </div>

            <div class="modal-footer mh-override">
                <input type="hidden" id="all-row-id-delete">
                <input type="hidden" id="all-id-delete">
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

        @if(Laratrust::can('update-leave-allocation'))
        $(document).on('click', '.la-edit-footer-btn', function(e){
			e.preventDefault();

            var btn = $(this),
                card = btn.closest('.card'),
                k = card.data('hrid');
            $("#card-footer-" + k).toggleClass('d-none');
        });

        $(document).on('click', '.la-edit-btn', function(e){

			e.preventDefault();

            var btn = $(this),
				btn_text = btn.html(),
                card = btn.closest('.card'),
                k = card.data('hrid'),
                id = card.data('id'),
                title = card.data('title'),
				val = $("#allowed-edit-" + k).val(),
                load_element = "#card-la-" + k,
				token ='{{ Session::token() }}',
				url = "{{route('leave-allocation.update', ':id')}}";
                url = url.replace(':id',id);

			$.ajax({
				type: "PUT",
				url: url,
				data: {
					id: id,
					val: val,
					_token: token
				},
				beforeSend: function () {
					btn.html('<i class="fas fa-spinner fa-spin"></i>');
				},
				success: function(response) {
					btn.html(btn_text);
                    $("#card-footer-" + k).toggleClass('d-none');
					swal_alert('{{$user->fullname}} ' + title + ' leave allocation updated','','success','Continue',2000);
                    $(load_element).load(location.href + " "+ load_element +">*","");
				},
				error: function(jqXHR, exception){
					btn.html(btn_text);
					var error = getErrorMessage(jqXHR, exception);
                    swal_alert('Failed to update {{$user->fullname}} ' + title + ' leave allocation',error,'error','Go Back');
				}
			});
        });
        @endif


        @if(Laratrust::can('delete-leave-allocation'))
        $('#delete-modal').on('show.bs.modal', function (e) {
			var btn = $(e.relatedTarget),
				card = btn.closest('.card'),
                delete_title = card.data('title'),
				k = card.data('hrid'),
				item_id = card.data('id');

			$("#delete-title").text(delete_title);
			$("#all-id-delete").val(item_id);
			$("#all-row-id-delete").val(k);
		});

		$(document).on('click', '#delete-btn', function(e){
			e.preventDefault();
			var btn = $(this),
				btn_text = btn.html(),
				item_id = $('#all-id-delete').val(),
                title = $("#delete-title").text(),
				remove_element = '#card-la-' + $("#all-row-id-delete").val(),
				load_element = '#loadDiv',
				token ='{{ Session::token() }}',
				url = "{{route('leave-allocation.destroy', ':id')}}";
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
                    swal_alert('{{$user->fullname}} ' + title + ' leave allocation deleted','','success','Continue',1500);
					$(remove_element).remove();
				},
				error: function(jqXHR, exception){
					btn.html(btn_text);
					var error = getErrorMessage(jqXHR, exception);
					swal_alert('Failed to delete {{$user->fullname}} ' + title + ' leave allocation',error,'error','Go Back');
				}
			});
        });
        @endif

    });

</script>

@endsection
