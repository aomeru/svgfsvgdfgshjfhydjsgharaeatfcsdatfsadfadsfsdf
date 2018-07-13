<?php
$color = [
    'pending' => 'muted',
    'submitted' => 'primary',
    'manager_approved' => 'success',
    'manager_declined' => 'danger',
    'hr_approved' => 'success',
    'hr_declined' => 'danger',
    'completed' => 'success',
];
$manager_stage = ['submitted'];
$hr_stage = ['manager_deferred','manager_approved'];
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
                <p class="text-right">
                    Application Status: <em class="text-{{$color[$item->status]}} text-capitalize">{{str_replace('_',' ',$item->status)}}</em>
                </p>
                <hr class="my-3">
                <div class="row">
                    <div class="col-6 col-sm-4">
                        <p><span class="text-secondary">Leave Type: </span>{{$item->leave->leave_type->title }}</p>
                    </div>
                    <div class="col-6 col-sm-4">
                        <p><span class="text-secondary">Start date: </span>{{ date('jS M, Y', strtotime($item->leave->start_date)) }}</p>
                    </div>

                    <div class="col-6 col-sm-4">
                        <p><span class="text-secondary">End date: </span>{{ date('jS M, Y', strtotime($item->leave->end_date)) }}</p>
                    </div>
                    <div class="col-6 col-sm-4">
                        <p><span class="text-secondary">Date of Resumption: </span>{{ date('jS M, Y', strtotime($item->leave->back_on)) }}</p>
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
                <div class="card">
                    <div class="card-header bg-dark text-white">
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


                        @if($item->manager->id == Auth::user()->id && in_array($item->status,$manager_stage))
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
                                                <input id="start-date" type="date" class="form-control" placeholder="Start Date" v-model="mindate" @change="set_max_date" min="{{ date('Y-m-d') }}">
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

                            <div class="alert text-center alert-dismissible fade show" role="alert" v-if="pmsg" v-bind:class="{ 'alert-danger': perror, 'alert-success': psuccess }">
                                <i class="fas mr-2" v-bind:class="{ 'fa-times': perror, 'fa-check': psuccess }"></i> @{{presponse}}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close" @click="unset_alert"><span aria-hidden="true">&times;</span></button>
                            </div>

                            <div v-if="defer">

                                <button class="btn btn-success mr-1" type="button" role="button" @click="process_action" v-html="adbtn"></button>

                                <button class="btn btn-default mr-1" type="button" role="button" @click="set_defer_state"><i class="fas fa-times mr-2"></i>Cancel</button>

                            </div>
                            <div v-else>

                                <button class="btn btn-success mr-1" type="button" role="button" @click="set_option('approve')" v-html="abtn"></button>

                                <button class="btn btn-secondary mr-1" type="button" role="button" @click="set_defer_state"><i class="fas fa-arrow-right mr-2"></i>Defer</button>

                                <button class="btn btn-danger mr-1" type="button" role="button" @click="set_option('decline')" v-html="dbtn"></button>

                            </div>

                            <hr class="my-3">
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
                                                $img = $x->type == 'sys' ? asset('images/brand-logo.png') : $x->user->photo;
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
        $('.data-table').DataTable({
            "order": []
        });
        $('.select').select2();

        var app = new Vue({
            el : '#app',
            data : {
                author : '{{$item->manage_decision_date == null ? "manager" : "hr" }}',
                pmode : '',
                hr : '',
                comment : '',
                defer : false,
                start_date : '',
                end_date : '{{$item->leave->end_date}}',
                mindate : '{{$item->leave->start_date}}',
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
                set_defer_state : function(){
                    this.defer = this.defer === true ? false : true
                },
                set_max_date : function(){
                    let vm = this
                    axios.post('/portal/leave/my-leave/get-date', {
                        params: { start_date: vm.mindate, allowed: {{$item->leave->user->leave_allocation()->where('leave_type_id',$item->leave->leave_type_id)->value('allowed')}}, _token: vm.token }
                    }).then(function(response) {vm.maxdate = response.data;}).catch(function(error){ console.log(error); });
                },
                set_start_date : function(){
                    this.start_date = this.mindate
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
                        pmode : this.pmode,
                        start_date : this.start_date,
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
                this.set_start_date()
                this.abtn = this.button.approve
                this.dbtn = this.button.decline
                this.adbtn = this.button.approve_defer
            },
            watch : {
                mindate : function(){
                    this.set_start_date()
                },
                defer : function(){
                    if(this.defer === true) this.pmode = 'defer'; else this.pmode = ''
                }
            }
        });



        // $('#edit-modal').modal('show');

        // $(document).on('click', '#edit-btn', function(e){

		// 	e.preventDefault();

        //     var btn = $(this),
		// 		btn_text = btn.html(),
		// 		users = $("#users").val(),
        //         user_unit = $("#user-unit").is(':checked'),
		// 		token ='{{ Session::token() }}',
		// 		url = "{{route('managers.update', ':id')}}";
        //         url = url.replace(':id',"{{Crypt::encrypt($item->id)}}");

		// 	$.ajax({
		// 		type: "PUT",
		// 		url: url,
		// 		data: {
		// 			user_unit: user_unit,
		// 			users: users,
		// 			_token: token
		// 		},
		// 		beforeSend: function () {
		// 			btn.html('<i class="fas fa-spinner fa-spin"></i>');
		// 		},
		// 		success: function(response) {
		// 			btn.html(btn_text);
        //             $('#edit-modal').modal('hide');
		// 			swal_alert('Manager subordinates updated','','success','Continue');
        //             window.setTimeout(function(){
        //                 window.location.href = "{{route('managers.show',Crypt::encrypt($item->id))}}";
        //             },1000);
		// 		},
		// 		error: function(jqXHR, exception){
		// 			btn.html(btn_text);
		// 			var error = getErrorMessage(jqXHR, exception);
        //             swal_alert('Failed to update Manager subordinates',error,'error','Go Back');
		// 		}
		// 	});
        // });

    });

</script>

@endsection
