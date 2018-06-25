@extends('layouts.portal')
@section('page_title','Leave Allocation - ')
@section('portal_page_title') <i class="fas fa-calendar mr-3"></i>Leave Allocation @endSection

@section('bc')
    <nav aria-label="breadcrumb" class="d-none d-md-block">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('portal')}}">Dashboard</a></li>
            <li class="breadcrumb-item">Leave</li>
            <li class="breadcrumb-item active" aria-current="page">Allocation</li>
        </ol>
    </nav>
@endSection

@section('content')

<div class="card">
    <div class="card-body">
        @if(Laratrust::can('create-leave-type'))
        <div class="mb-3 d-flex justify-content-end">
            <button class="btn btn-primary btn-sm no-margin" title="Create new leave type" data-toggle="modal" data-target="#add-modal"><i class="fas fa-user-plus mr-2"></i>Allocate</button>
        </div>
        @endif

        @if ($list->count() == 0)
            <div class="alert alert-info" role="role">No leave allocation record found.</div>
        @else

            <div class="table-responssive">

                <table class="table table-striped table-bordered table-hover nowwrap data-table" width="100%" data-page-length="25">

                    <thead>
                        <tr class="active">
                            <th>#</th>
                            <th>User</th>
                            @foreach($ltypes as $ltype)
                            <th class="text-center">{{$ltype->title}}</th>
                            @endforeach
                            <th class="text-center">Year</th>
                            @if(Laratrust::can('update-leave-allocation|delete-leave-allocation'))
                                <th class="text-right">Actions</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>

                        @php $row_count = 1 @endphp

                        @foreach($users as $item)

                            <tr>

                                <td>{{ $row_count }}</td>

                                <td>{{ $item->firstname.' '.$item->lastname }}</td>

                                @for($x=0;$x<$ltypes->count();$x++)
                                <?php
                                $rec = $item->leave_allocation()->where('leave_type_id',$ltypes[$x]['id'])->first();
                                ?>
                                <td class="text-center">
                                    {!! $rec == null ? '<span class="text-muted">N/A</span>' : $rec->allowed !!}
                                </td>
                                @endfor

                                <td class="text-center">
                                    {!! $rec == null ? '<span class="text-muted">N/A</span>' : $rec->year !!}
                                </td>

                                @if(Laratrust::can('update-leave-allocation'))
                                <td class="text-right">
                                    @if(Laratrust::can('update-leave-type'))<a class="btn btn-primary btn-sm text-white" title="Edit {{ $item->title }}"><i class="fas fa-pencil-alt"></i></a>@endif
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
