@extends('layouts.portal')
@section('page_title','Users - ')
@section('portal_page_title') <i class="fas fa-user-circle mr-3"></i>All Users @endSection

@section('bc')
    <nav aria-label="breadcrumb" class="d-none d-md-block">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('portal')}}">Dashboard</a></li>
            <li class="breadcrumb-item">Users</li>
            <li class="breadcrumb-item active" aria-current="page">All Userss</li>
        </ol>
    </nav>
@endSection

@section('content')

<div class="card">
    <div class="card-body">
        <div class="mb-3 d-flex justify-content-end">
            <button class="btn btn-primary btn-sm no-margin" title="Add new user" data-toggle="modal" data-target="#add-user-modal"><i class="fa fa-plus"></i></button>
        </div>

        @if ($list->count() == 0)
            <p class="alert alert-info">No user record found.</p>
        @else

            <div class="table-responsive">

                <table class="table table-striped table-bordered table-hover nowrap data-table" width="100%" data-page-length="50">

                    <thead>
                        <tr class="active">
                            <th>#</th>
                            <th>ID</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Job Title</th>
                            <th>Department</th>
                            <th>Employee Type</th>
                            <th>Employee Since</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @php $row_count = 1 @endphp

                        @foreach($list as $item)

                            <tr id="row-{{$item->id}}" data-hrid="{{$item->id}}" data-id="{{Crypt::encrypt($item->id)}}" data-firstname="{{$item->firstname}}" data-lastname="{{$item->lastname}}" data-staff-id="{{$item->staff_id}}" data-unit-id="{{$item->unit == null ? '' : $item->unit->title}}"  data-email="{{$item->email}}" data-status="{{$item->status}}">

                                <td>{{ $row_count }}</td>

                                <td>{!! $item->staff_id == null ? '<span class="c-999">N/A</span>' : $item->staff_id !!}</td>

                                <td>{!! $item->firstname == null ? '<span class="c-999">N/A</span>' : $item->firstname.' '.$item->lastname !!}</td>

                                <td><u><a href="{{route('portal.users.show', Crypt::encrypt($item->id))}}" class="c-06f">{{$item->email}}</a></u></td>

                                <td>{{ $item->job_title }}</td>

                                <td>{!! $item->unit == null ? '<span class="c-999">N/A</span>' : $item->unit->department->title.' <span class="c-999 v-padding-5">/</span> '.$item->unit->title !!}</td>

                                <td>{{ $item->employee_type }}</td>

                                <td>{{date('d-m-y', strtotime($item->date_of_hire))}}</td>

                                <td class="text-right">
                                    <button class="btn btn-primary btn-sm" title="Edit {{ $item->firstname }}" data-toggle="modal" data-target="#edit-user-modal"><i class="fas fa-pencil-alt"></i></button>

                                    <button class="btn btn-danger btn-sm" title="Delete {{ $item->firstname }}" data-toggle="modal" data-target="#delete-user-modal"><i class="far fa-trash-alt"></i></button>
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

@endsection







@section('scripts')

<script>
    $(document).ready(function() {
        $('.data-table').DataTable();
    });

</script>

@endsection
