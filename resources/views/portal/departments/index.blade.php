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

                                        <tr id="row-{{$item->id}}" data-hrid="{{$item->id}}" data-item-id="{{Crypt::encrypt($item->id)}}" data-item-title="{{$item->title}}">
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

                                        <tr id="urow-{{$unit->id}}" data-hrid="{{$unit->id}}" data-item-id="{{Crypt::encrypt($unit->id)}}" data-item-title="{{$unit->title}}" data-item-dtitle="{{$unit->department->title}}">
                                            <td>{{ $row_count }}</td>
                                            <td><u><a href="{{route('portal.depts.show.unit', Crypt::encrypt($unit->id))}}" class="c-06f">{{ $unit->title }}</a></u></td>
                                            <td>{{ $unit->department->title }}</td>
                                            <td>
                                                @if($item->manager != null)
                                                    {{$item->manager->firstname.' '.$item->manager->lastname}}
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
<div class="modal fade" id="add-dept-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog w300 sm-w400" role="document">
		<div class="modal-content">
			<form method="post">

				<div class="modal-header">
					<h5 class="modal-title no-padding no-margin font-weight-bold">Add Department</h5>
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
                                        <option value="{{Crypt::encrypt($user->email)}}">{{$user->firstname.' '.$user->lastname}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
					</div>
				</div>

				<div class="modal-footer mh-override">
                    <button type="button" class="btn-default btn" data-dismiss="modal" aria-label="Close"><i class="fa fa-times mr5"></i>Cancel</button>
                    <button class="btn-primary btn" id='add-dept-btn' type="submit" role="button"><i class="fa fa-check mr5"></i>Add</button>
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
    });

</script>

@endsection
