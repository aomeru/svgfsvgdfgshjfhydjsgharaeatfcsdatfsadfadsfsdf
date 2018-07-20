<?php
$color = [
    'pending' => 'muted',
    'submitted' => 'primary',
    'manager_approved' => 'success',
    'manager_deferred' => 'success',
    'manager_declined' => 'danger',
    'hr_approved' => 'success',
    'hr_deferred' => 'success',
    'hr_declined' => 'danger',
    'completed' => 'success',
    'called-off' => 'warning',
];
?>

@extends('layouts.portal')
@section('page_title','Leave - ')
@section('portal_page_title') <i class="fas fa-calendar-check mr-3"></i>Leave Request @endSection

@section('bc')
    <nav aria-label="breadcrumb" class="d-none d-md-block">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('portal')}}">Dashboard</a></li>
            <li class="breadcrumb-item">Leave</li>
            <li class="breadcrumb-item active" aria-current="page">Requests</li>
        </ol>
    </nav>
@endSection

@section('content')

    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="card-title m-0">Leave Requests</h5>
        </div>
        <div class="card-body">
            @if($list->count() == 0)
                <p class="alert alert-secondary mb-0">
                    You have no created leave record
                </p>
            @else
                <div class="table-responsive">

                    <table class="table table-striped table-bordered table-hover nowrap data-table" width="100%" data-page-length="25">

                        <thead>
                            <tr class="active">
                                <th>#</th>
                                <th>Leave</th>
                                <th>User</th>
                                <th class="text-center">Start Date</th>
                                <th class="text-center">End Date</th>
                                <th class="text-center">Return Date</th>
                                <th>Manager</th>
                                <th>HR</th>
                                <th class="text-center"><i class="fas fa-comments"></i></th>
                                <th class="text-center">Last Modified</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>

                        <tbody>

                            @php $row_count = 1 @endphp

                            @foreach($list as $item)

                                <tr>

                                    <td>{{ $row_count }}</td>

                                    <td class="text-uppercase">
                                        <a href="{{ route('portal.leave.request.show', $item->code) }}" class="text-underline" title="View {{ $item->leave->user->fullname }} leave request">
                                            {{ $item->code }}
                                        </a>
                                    </td>

                                    <td>{{ $item->leave->user->fullname }}</td>

                                    <td class="text-center">{{ date('jS M, Y', strtotime($item->leave->start_date)) }}</td>

                                    <td class="text-center">{!! $item->leave->end_date == null ? '<em class="text-muted">N/A</em>' : date('jS M, Y', strtotime($item->leave->end_date)) !!}</td>

                                    <td class="text-center">{!! $item->leave->back_on == null ? '<em class="text-muted">N/A</em>' : date('jS M, Y', strtotime($item->leave->back_on)) !!}</td>

                                    <td>{{ $item->manager->fullname }}</td>

                                    <td>{{ $item->hr != null ? $item->hr->fullname : '' }}</td>

                                    <td class="text-center">{{ $item->log->count() }}</td>

                                    <td class="text-center">{{\Carbon\Carbon::parse($item->updated_at)->diffForHumans()}}</td>

                                    <td class="text-center text-{{$color[$item->status]}}">{{ $item->status }}</td>

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
