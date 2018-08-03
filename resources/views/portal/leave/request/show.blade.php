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
$manager_stage = ['submitted'];
$hr_stage = ['manager_deferred','manager_approved','hr_declined'];
$edit = false;
$user_edit = in_array($item->status,['submitted','manager_declined']) && Auth::id() == $item->leave->user_id ? true : false;
$call_off_request = in_array($item->status,['hr_approved','hr_deferred']) && Auth::id() == $item->leave->user_id && date('Y-m-d') < $item->end_date ? true : false;
?>

@extends('layouts.portal')
@section('page_title','Leave Request: '.$item->code.' - ')
@section('portal_page_title') <i class="fas fa-user-tie mr-3"></i>Leave Request: <span class="text-primary">{{$item->code}}</span> @endSection

@section('bc')
    <nav aria-label="breadcrumb" class="d-none d-md-block">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('portal')}}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{route('portal.users')}}">Leave</a></li>
            <li class="breadcrumb-item"><a href="{{route('portal.leave.request')}}">Requests</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $item->code }}</li>
        </ol>
    </nav>
@endSection

@section('content')

<div class="row">
    <div class="col-sm-3">
        @include('partials.portal.profile',['userdata' => $item->leave->user])
        <div class="card">
            <div class="card-body">
                @if(Laratrust::can('update-leave-allocation'))
                    <a href="{{ route('leave-allocation.show', Crypt::encrypt($item->leave->user->id)) }}" class="btn btn-info btn-sm text-white" title="View {{ $item->leave->user->fullname }} leave allocations"><i class="fas fa-eye"></i> View Allocations</a>
                @endif
                @if(Laratrust::can('read-leave-record'))
                    <a href="" class="btn btn-info btn-sm text-white" title="View {{ $item->leave->user->fullname }} leave record"><i class="fas fa-eye"></i> View Records</a>
                @endif
            </div>
        </div>
    </div>

    <div class="col-sm-9">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="card-title mb-0">Application</h5>
            </div>
            <div class="card-body">
                <div class="@if($user_edit) d-flex justify-content-between @endif">
                    @if($user_edit)
                        <a href="{{ route('portal.leave.edit', Crypt::encrypt($item->leave->id)) }}" class="btn btn-primary btn-sm" title="Edit {{ Auth::user()->fullname.'-'.strtotime($item->leave->created_at) }} leave"><i class="fas fa-pencil-alt mr-2"></i> Edit</a>
                    @endif
                    <p class="text-right mb-0">
                        Application Status: <em class="text-{{$color[$item->status]}} text-capitalize">{{str_replace('_',' ',$item->status)}}</em>
                    </p>
                </div>
                <hr class="my-3">
                <div class="row text-primary">
                    <div class="col-6 col-sm-4">
                        <p>
                            <span class="text-secondary">Leave Type: </span>{{$item->leave->leave_type->title }}
                        </p>
                    </div>
                    <div class="col-6 col-sm-4">
                        <p>
                            <span class="text-secondary">Start date: </span>
                            {!! $item->deference == null ? date('jS M, Y', strtotime($item->leave->start_date)) : '<em><s class="text-danger">'.date('jS M, Y', strtotime($item->leave->start_date)).'</s></em> '.date('jS M, Y', strtotime($item->deference->start_date)) !!}
                        </p>
                    </div>

                    <div class="col-6 col-sm-4">
                        <p>
                            <span class="text-secondary">End date: </span>
                            {!! $item->deference == null ? date('jS M, Y', strtotime($item->leave->end_date)) : '<em><s class="text-danger">'.date('jS M, Y', strtotime($item->leave->end_date)).'</s></em> '.date('jS M, Y', strtotime($item->deference->end_date)) !!}
                        </p>
                    </div>
                    <div class="col-6 col-sm-4">
                        <p>
                            <span class="text-secondary">Date of Resumption: </span>
                            {!! $item->deference == null ? date('jS M, Y', strtotime($item->leave->back_on)) : '<em><s class="text-danger">'.date('jS M, Y', strtotime($item->leave->back_on)).'</s></em> '.date('jS M, Y', strtotime($item->deference->back_on)) !!}
                        </p>
                    </div>

                    <div class="col-6 col-sm-4">
                        <p><span class="text-secondary">Manager: </span>{{$item->manager->fullname}}</p>
                    </div>
                    <div class="col-6 col-sm-4">
                        <p><span class="text-secondary">Relieving Staff: </span>{{$item->leave->ruser->fullname}}</p>
                    </div>
                </div>
            </div>
        </div>



        <div class="row mt-3">
            <div class="col-sm-6">
                @if($item->deference != null)
                    <div class="card mb-3">
                        <div class="card-header bg-success text-white">
                            <h5 class="card-title mb-0">Leave Deference</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-primary">
                                <div class="col-6">
                                    <p class="mb-0"><span class="text-secondary">Start date: </span>{{ date('jS M, Y', strtotime($item->deference->start_date)) }}</p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-0"><span class="text-secondary">End date: </span>{{ date('jS M, Y', strtotime($item->deference->end_date)) }}</p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-0"><span class="text-secondary">Date of Resumption: </span>{{ date('jS M, Y', strtotime($item->deference->back_on)) }}</p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-0 text-capitalize"><span class="text-secondary">Decision by: </span>{{ $item->deference->type }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Employee Leave Action</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6"><p class="text-muted mb-0">Applications: <span class="text-dark">{{$item->leave->user->leave()->count()}}</span></p></div>
                            <div class="col-6"><p class="text-muted mb-0">Approved: <span class="text-dark">{{$item->leave->user->leave()->whereIn('status',['completed','hr_approved','hr_deferred'])->count()}}</span></p></div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <p class="text-muted mb-0">
                                    Last Application:
                                    <span class="text-dark">
                                        <?php
                                        $last_leave = $item->leave->user->leave()->has('leave_request')->where('id','<>',$item->leave->id)->orderby('created_at','desc')->first();
                                        ?>
                                        {!! $last_leave == null ? '<em class="text-muted">N/A</em>' : $last_leave->leave_request->code !!}
                                    </span>
                                </p>
                            </div>
                        </div>
                         <div class="row">
                            <div class="col-6"><p class="text-muted mb-0">Manager Decision: <span class="text-{{$color[$item->status]}} text-capitalize">{{str_replace('manager ','',str_replace('_',' ',$item->manager_decision))}}</span></p></div>
                            <div class="col-6"><p class="text-muted mb-0">HR Decision: <span class="text-{{$color[$item->status]}} text-capitalize">{{str_replace('hr ','',str_replace('_',' ',$item->hr_decision))}}</span></p></div>
                        </div>


                        @if($item->manager_id == Auth::user()->id && in_array($item->status,$manager_stage))
                            <hr class="my-3">
                            <h5 class="text-primary">Manager Action</h5>
                            <div class="form-group">
                                <select class="custom-select" style="width: 100%;" v-model="hr">
                                    <option value="">Select Leave Approval Staff</option>
                                    @foreach($lms as $lm)
                                        <option value="{{$lm->email}}">{{$lm->fullname}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <textarea rows="3" class="form-control" placeholder="Comment" v-model="comment"></textarea>
                            </div>
                            <div v-if="defer">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="start-date" class="form-control-label">Deferred Start Date</label>
                                            <div class="input-group">
                                                <input id="start-date" type="date" class="form-control" placeholder="Start Date" v-model="mindate" min="{{ date('Y-m-d') }}">
                                                <label for="start-date" class="input-group-append mb-0">
                                                    <span class="input-group-text"><span class="fas fa-calendar"></span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="start-date" class="form-control-label">Deferred End Date</label>
                                            <div class="input-group">
                                                <input id="end-date" type="date" class="form-control" placeholder="End Date" :min="mindate" :max="maxdate" v-model="end_date">
                                                <label for="end-date" class="input-group-append mb-0">
                                                    <span class="input-group-text"><span class="fas fa-calendar"></span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-danger alert-dismissible fade show" role="alert" v-if="perror">
                                <p class="mb-0" v-html="presponse"></p>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close" @click="unset_alert"><span aria-hidden="true">&times;</span></button>
                            </div>

                            <div v-if="defer">
                                <button class="btn btn-success mr-1" type="button" role="button" @click="process_action" v-html="adbtn"></button>

                                <button class="btn btn-default mr-1" type="button" role="button" @click="defer = false"><i class="fas fa-times mr-2"></i>Cancel</button>
                            </div>

                            <div v-else>

                                <button class="btn btn-success mr-1" type="button" role="button" @click="set_option('approve')" v-html="abtn"></button>

                                <button class="btn btn-secondary mr-1" type="button" role="button" @click="defer = true"><i class="fas fa-arrow-right mr-2"></i>Defer</button>

                                <button class="btn btn-danger mr-1" type="button" role="button" @click="set_option('decline')" v-html="dbtn"></button>

                            </div>

                            <hr class="my-3">
                        @endif


                        @if($item->hr_id == Auth::user()->id && in_array($item->status,$hr_stage))
                            <hr class="my-3">
                            <h5 class="text-primary">HR Action</h5>

                            <div class="form-group">
                                <textarea rows="3" class="form-control" placeholder="Comment" v-model="comment"></textarea>
                            </div>
                            <div v-if="defer">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="start-date" class="form-control-label">Deferred Start Date</label>
                                            <div class="input-group">
                                                <input id="start-date" type="date" class="form-control" placeholder="Start Date" v-model="mindate" min="{{ date('Y-m-d') }}">
                                                <label for="start-date" class="input-group-append mb-0">
                                                    <span class="input-group-text"><span class="fas fa-calendar"></span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="start-date" class="form-control-label">Deferred End Date</label>
                                            <div class="input-group">
                                                <input id="end-date" type="date" class="form-control" placeholder="End Date" :min="mindate" :max="maxdate" v-model="end_date">
                                                <label for="end-date" class="input-group-append mb-0">
                                                    <span class="input-group-text"><span class="fas fa-calendar"></span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-danger alert-dismissible fade show" role="alert" v-if="perror">
                                <p class="mb-0" v-html="presponse"></p>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close" @click="unset_alert"><span aria-hidden="true">&times;</span></button>
                            </div>

                            <div v-if="defer">
                                <button class="btn btn-success mr-1" type="button" role="button" @click="process_action" v-html="adbtn"></button>

                                <button class="btn btn-default mr-1" type="button" role="button" @click="defer = false"><i class="fas fa-times mr-2"></i>Cancel</button>
                            </div>

                            <div v-else>

                                <button class="btn btn-success mr-1" type="button" role="button" @click="set_option('approve')" v-html="abtn"></button>

                                <button class="btn btn-secondary mr-1" type="button" role="button" @click="defer = true"><i class="fas fa-arrow-right mr-2"></i>Defer</button>

                                <button class="btn btn-danger mr-1" type="button" role="button" @click="set_option('decline')" v-html="dbtn"></button>

                            </div>

                            <hr class="my-3">
                        @endif


                        @if($call_off_request)
                            <hr class="my-3">
                            <a href="" class="btn btn-warning" title="Call off leave request"><i class="fas fa-angle-double-right mr-2"></i>Request Call-Off</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-body">
                        @if($item->log != null)

                            <table class="table data-table" width="100%" data-page-length="10" data-order="2">

                                <thead>
                                    <th class="text-center"></th>
                                    <th>Comment</th>
                                    <th class="text-right">Date</th>
                                </thead>

                                <tbody>

                                    @foreach($item->log()->orderby('created_at','desc')->get() as $x)

                                        <tr>

                                            <td class="text-center">
                                                <?php
                                                $img = $x->type == 'sys' ? asset('images/brand-logo.png') : 'data:image.jpg;base64,'.$x->user->photo;
                                                if($img == '') $img = asset('images/user.png');
                                                ?>
                                                <img src="{{$img}}" class="rounded-circle border border-light" alt="" width="50px" height="50px">
                                            </td>

                                            <td><em class="text-muted">{{ $x->type == 'sys' ? 'Automated Message' : $x->user->fullname }}</em> <br> {{$x->comment}}</td>

                                            <td class="text-right text-muted">{{\Carbon\Carbon::parse($x->created_at)->diffForHumans()}}</td>

                                        </tr>

                                    @endforeach

                                    <tr>

                                        <td class="text-center">
                                            <img src="{{$item->leave->user->photo == null ? asset('images/user.png') : 'data:image.jpg;base64,'.$item->leave->user->photo}}" class="rounded-circle border border-light" alt="" width="50px" height="50px">
                                        </td>

                                        <td><em class="text-muted">{{ $item->leave->user->fullname }}</em> <br> {{$item->leave->comment}}</td>

                                        <td class="text-right text-muted">{{\Carbon\Carbon::parse($item->created_at)->diffForHumans()}}</td>

                                    </tr>

                                </tbody>

                            </table>

                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection






@section('page_footer')


@endsection







@section('scripts')

<script>

    $(document).ready(function() {

        var app = new Vue({
            el : '#portal',
            data : {
                author : '{{in_array($item->status, ["submitted","manager_declined"]) ? "manager" : "hr" }}',
                pmode : '',
                hr : '',
                comment : '',
                defer : false,
                end_date : '{{$item->deference == null ? $item->leave->end_date : $item->deference->end_date}}',
                mindate : '{{$item->deference == null ? $item->leave->start_date : $item->deference->start_date}}',
                maxdate : '',
                token : '{{ Session::token() }}',
                button : {
                    loading : '<i class="fas fa-spinner fa-spin"></i>',
                    approve : '<i class="fas fa-check mr-2"></i>Approve',
                    decline : '<i class="fas fa-times mr-2"></i>Decline',
                    approve_defer : '<i class="fas fa-check mr-2"></i>Approve with Deference',
                },
                abtn : '',
                dbtn : '',
                adbtn : '',
                pmsg : false,
                perror : false,
                psuccess : false,
                presponse : '',
            },
            methods : {
                set_option : function(v){
                    this.pmode = v
                    this.process_action()
                },
                set_max_date : function(){
                    let self = this
                    axios.post('/portal/leave/my-leave/get-date', {
                        start_date : this.mindate,
                        ltype : '{{Crypt::encrypt($item->leave->user->leave_allocation()->where("leave_type_id",$item->leave->leave_type->id)->value("id"))}}',
                    }).then((response) => {
                        self.maxdate = response.data
                    }).catch((error) => {
                        console.log(error)
                    });
                },
                set_loading : function () {
                    if(this.pmode === 'approve') {
                        this.abtn = this.button.loading
                    } else if (this.pmode === 'decline') {
                        this.dbtn = this.button.loading
                    } else this.adbtn = this.button.loading
                },
                unset_loading : function () {
                    if(this.pmode === 'approve') {
                        this.abtn = this.button.approve
                    } else if (this.pmode === 'decline') {
                        this.dbtn = this.button.decline
                    } else this.adbtn = this.button.approve_defer
                },
                unset_alert: function(){
                    this.pmsg = this.perror = this.psuccess = false
                    this.presponse = ''
                },
                process_action: function(){
                    this.set_loading()
                    this.unset_alert()
                    let url = this.author === 'manager' ? '/portal/leave/requests/manager-decision' : '/portal/leave/requests/hr-decision'
                    let self = this
                    axios.post(url, {
                        code : '{{$item->code}}',
                        hr : this.hr,
                        author : this.author,
                        pmode : this.pmode,
                        start_date : this.mindate,
                        end_date : this.end_date,
                        comment : this.comment
                    }).then((response) => {
                        // self.unset_loading()
                        // self.pmsg = self.psuccess = true
                        // self.presponse = response.data
                        window.location.href = "{{route('portal.leave.request.show', $item->code)}}"
                    }).catch((error) => {
                        self.unset_loading()
                        self.pmsg = self.perror = true
                        self.presponse = get_error_msg(error)
                    });
                }
            },
            created(){
                this.set_max_date()
                this.abtn = this.button.approve
                this.dbtn = this.button.decline
                this.adbtn = this.button.approve_defer
            },
            watch : {
                mindate : function(){
                    this.set_max_date()
                },
                defer : function(){
                    if(this.defer === true) this.pmode = 'defer'; else this.pmode = ''
                }
            },
            mounted(){
                $('.data-table').DataTable({
                    "order": []
                });
                $('.select').select2();
            }
        });
    });

</script>

@endsection
