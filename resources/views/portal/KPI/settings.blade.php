@extends('layouts.portal')
@section('page_title','KPI Settings - ')
@section('portal_page_title') <i class="fas fa-chart-line mr-3"></i>KPI Settings @endSection

@section('bc')
    <nav aria-label="breadcrumb" class="d-none d-md-block">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('portal')}}">Dashboard</a></li>
            <li class="breadcrumb-item">KPI</li>
            <li class="breadcrumb-item active" aria-current="page">Settings</li>
        </ol>
    </nav>
@endSection

@section('content')

<div class="row">
    <div :class="col_right">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="card-title mb-0">Settings</h5>
            </div>
            <div class="card-body">
                <div class="mb-3 d-flex justify-content-end" v-if="action == false">
                    <button class="btn btn-primary btn-sm no-margin" title="Add new user" @click="set_create"><i class="fa fa-plus"></i></button>
                </div>
                <div v-if="list.length == 0">
                    <p class="alert alert-info mb-0">No kpi settings found.</p>
                </div>
                <div v-else class="table-responsive">
                    <table class="table table-striped table-bordered table-hover nowwrap data-table" width="100%" data-page-length="25">

                        <thead>
                            <tr class="active">
                                <th>#</th>
                                <th>Title</th>
                                <th>Value</th>
                                @if(Laratrust::can('update-kpi-settings'))<th class="text-right">Actions</th>@endif
                            </tr>
                        </thead>

                        <tbody>

                            <tr v-for="(item, key) in list" data-title="@{!! item.title !!}" data-value="@{!! item.tvalue !!}">
                                <td>@{{key + 1}}</td>
                                <td>@{{item.title}}</td>
                                <td>@{{item.value}}</td>
                                <td></td>
                            </tr>

                            {{-- @foreach($list as $key $item)

                                <tr id="row-{{$item->id}}" data-hrid="{{$item->id}}" data-id="{{Crypt::encrypt($item->id)}}" data-fullname="{{$item->firstname.' '.$item->lastname}}" data-doh="{{$item->date_of_hire}}" data-emp-type="{{$item->employee_type}}" data-staff-id="{{$item->staff_id}}" data-unit-id="{{$item->unit == null ? '' : $item->unit->title}}" data-manager="{{$item->manager == null ? '' : $item->manager->manager->email}}" data-status="{{$item->status}}">


                                    <td>{!! $item->staff_id == null ? '<span class="c-999">N/A</span>' : $item->staff_id !!}</td>

                                    <td>{!! $item->firstname == null ? '<span class="c-999">N/A</span>' : $item->firstname.' '.$item->lastname !!}</td>

                                    <td><u><a href="{{route('portal.users.show', Crypt::encrypt($item->id))}}" class="c-06f">{{$item->email}}</a></u></td>

                                    <td>{{ $item->job_title }}</td>

                                    <td>{!! $item->unit == null ? '<span class="c-999">N/A</span>' : $item->unit->title !!}</td>

                                    <td>{!! $item->unit == null ? '<span class="c-999">N/A</span>' : $item->unit->department->title !!}</td>

                                    <td>{!! $item->manager == null ? '<span class="c-999">N/A</span>' : $item->manager->manager->firstname.' '.$item->manager->manager->lastname !!}</td>

                                    <td>{{ $item->employee_type }}</td>

                                    <td>{{date('M jS, Y', strtotime($item->date_of_hire))}}</td>

                                    <td>{{ $item->status }}</td>

                                    @if(Laratrust::can('update-user'))
                                    <td class="text-right">
                                        <button class="btn btn-primary btn-sm" title="Edit {{ $item->firstname }}" data-toggle="modal" data-target="#edit-user-modal"><i class="fas fa-pencil-alt"></i></button>

                                        {{-- <button class="btn btn-danger btn-sm" title="Delete {{ $item->firstname }}" data-toggle="modal" data-target="#delete-user-modal"><i class="far fa-trash-alt"></i></button>
                                    </td>
                                    @endif

                                </tr>

                                @php $row_count++ @endphp

                            @endforeach --}}

                        </tbody>

                    </table>

                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-3" v-if="action">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">@{{action_title}}</h5>
            </div>
            <form @submit.prevent>
                <div class="card-body">

                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-sm-6">
                            <button class="btn btn-block btn-success"><i class="fas fa-check mr-2"></i>Create</button>
                        </div>
                        <div class="col-sm-6">
                            <button class="btn btn-block btn-outline-secondary" @click="clear_action"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>



@endsection







@section('scripts')

<script>
    $(document).ready(function() {
        new Vue({
            el : '#portal',
            data : {
                action : false,
                action_title : '',
                edit_state : false,
                create_state : false,
                col_left : 'col-sm-3',
                col_right : 'col-sm-12',
                list : {!! $list->toJson() !!},
            },
            methods : {
                set_create : function(){
                    this.action = this.create_state = true
                    this.action_title = 'Create KPI Setting'
                },
                clear_action : function(){
                    this.action = this.create_state = this.edit_state = false
                    this.action_title = ''
                }
            },
            mounted(){
                $('#data-table').DataTable({});
            },
            watch : {
                action : function(){
                    if(this.action)
                    {
                        this.col_right = 'col-sm-9'
                    } else {
                        this.col_right = 'col-sm-12'
                    }
                }
            }
        });
    });

</script>

@endsection
