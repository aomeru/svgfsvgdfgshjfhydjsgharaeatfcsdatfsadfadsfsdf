@extends('layouts.portal')
@section('page_title','Edit Leave Application - ')
@section('portal_page_title') <i class="fas fa-calendar-plus mr-3"></i>Edit Leave Application <span class="text-primary">{{$leave->leave_request->code}}</span> @endSection

@section('bc')
    <nav aria-label="breadcrumb" class="d-none d-md-block">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('portal')}}">Dashboard</a></li>
            <li class="breadcrumb-item">Leave</li>
            <li class="breadcrumb-item"><a href="{{route('portal.leave')}}">My Leave</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Application</li>
        </ol>
    </nav>
@endSection

@section('content')

<div class="row">
    <div class="col-sm-3 mb-3 mb-sm-0">
        <?php
        $color = 'info';
        $marker = round($la->leave_type->allowed/3);
        if($la->allowed <= $marker) $color = 'danger'; elseif($la->allowed <= ($marker* 2)) $color = 'warning';
        ?>
        <div class="card shadow-sm progress-bar-striped bg-{{$color}} border-0">
            <div class="card-header text-white border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title ttext-capitalize m-0">{{$la->leave_type->title}}</h5>
                    <div class="display-4">{{$la->allowed}}</div>
                </div>
            </div>
            <div class="card-body card-body-trr text-whitee bg-darkk p-3">
                <p class="mb-0">
                    <span class="text-secondary">Applications: </span>
                    <span>
                        <?php
                        $acount = Auth::user()->leave()->where('leave_type_id',$la->leave_type->id)->where('status','<>','pending')->count();
                        ?>
                        {{$acount}}
                    </span>
                </p>
                <p class="mb-0">
                    <span class="text-secondary">Taken: </span>
                    <span>
                        <?php
                        $tcount = Auth::user()->leave()->where('leave_type_id',$la->leave_type->id)->whereIn('status', ['called off','completed'])->count();
                        ?>
                        {{$tcount}}
                    </span>
                </p>
                <p class="mb-0">
                    <span class="text-secondary">Cancelled: </span>
                    <span>
                        <?php
                        $ccount = Auth::user()->leave()->where('leave_type_id',$la->leave_type->id)->whereIn('status', ['cancelled'])->count();
                        ?>
                        {{$ccount}}
                    </span>
                </p>
                <p class="mb-0">
                    <span class="text-secondary">Total Days Taken: </span><span>0</span>
                </p>
            </div>
        </div>
    </div>

    <div class="col-sm-9">
        <div class="card text-center">
            <div class="card-header bg-dark text-white">
                <ul class="nav nav-tabs card-header-tabs" id="nav-tabs" role="tablist">
                    <li class="nav-item">
                        <h5 class="nav-link active card-title mb-0" id="leave-application-tab" data-toggle="tab" role="tab" href="#leave-application" aria-controls="leave-application" aria-selected="true" style="cursor: pointer">Application</h5>
                    </li>
                    <li class="nav-item">
                        <h5 class="nav-link card-title mb-0" id="leave-submit-tab" data-toggle="tab" role="tab" href="#leave-submit" aria-controls="leave-submit" aria-selected="false" style="cursor: pointer">Submit</h5>
                    </li>
                </ul>
            </div>
            <div class="card-body">

                <div class="progress mb-2">
                    <div id="pbar" class="progress-bar progress-bar-striped bg-info progress-bar-animated" role="progressbar" :style="width" :aria-valuenow="pval" aria-valuemin="0" aria-valuemax="100">@{{ pval }}%</div>
                </div>

                <div id="alertDiv" class="mb-0"></div>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade text-center py-4 show active" id="leave-application" role="tabpanel" aria-labelledby="leave-application-tab">
                        <h5 class="text-primary">
                            <span class="text-uppercase">{{$leave->user->username.'-'.strtotime($leave->created_at).' '.$leave->leave_type->title}}</span>
                            <br><small class="text-muted">Please continue with your application</small>
                        </h5>
                        <hr class="my-3">
                        <div class="row">
                            <div class="col-sm-6 offset-sm-3">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="start-date" class="form-control-label">Start Date</label>
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
                                            <label for="start-date" class="form-control-label">End Date</label>
                                            <div class="input-group">
                                                <input id="end-date" type="date" class="form-control" placeholder="End Date" :min="mindate" :max="maxdate" value="{{$leave->end_date}}">
                                                <label for="end-date" class="input-group-append mb-0">
                                                    <span class="input-group-text"><span class="fas fa-calendar"></span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="rstaff" class="form-control-label">Relieving Staff</label>
                                    <select id="rstaff" class="form-control select" style="width: 100%;">
                                        <option>Select Staff</option>
                                        @foreach($col as $c)
                                            <option value="{{$c->email}}" @if($leave->ruser != null && $c->email == $leave->ruser->email) selected @endif>{{$c->fullname}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- <div class="row">
                                    <div class="col-6">
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">

                                        </div>
                                    </div>
                                </div> --}}
                            </div>
                        </div>

                        <hr class="my-3">

                        <a class="btn-secondary btn mr-2" href="{{route('portal.leave')}}"><i class="fas fa-sign-out-alt mr-2"></i>Exit</a>
                        <button class="btn-primary btn save-btn" data-action="continue" type="button" role="button"><i class="fas fa-save mr-2"></i>Continue</button>
                    </div>
                    <div class="tab-pane fade text-left text-center py-4" id="leave-submit" role="tabpanel" aria-labelledby="leave-submit-tab">
                        <h5 class="text-primary">
                            <span class="text-uppercase">{{$leave->user->username.'-'.strtotime($leave->created_at).' '.$leave->leave_type->title}}</span>
                            <br><small class="text-muted">Please add any neccessary information and submit</small>
                        </h5>
                        <hr class="my-3">
                        <div class="row">
                            <div class="col-sm-6 offset-sm-3">

                                <h5>Submit your application to your manager "<span class="text-primary">{{Auth::user()->manager == null ? 'No Manager':  Auth::user()->manager->manager->fullname}}</span>" for approval</h5>
                                <p class="help text-muted">Please contact HR if your manager information is incorrect</p>

                                <div class="form-group">
                                <textarea name="lcomment" id="lcomment" class="form-control" rows="5" placeholder="Additional Information?">{{$leave->comment}}</textarea>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">
                        <button class="btn-success btn save-btn" data-action="submit" type="button" role="button" @if(Auth::user()->manager == null) disabled @endif><i class="fas fa-check mr-2"></i>Submit</button>
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
            el : '#app',
            data : {
                mindate : '{{$leave->start_date}}',
                maxdate : '',
                token : '{{ Session::token() }}',
                pval : '',
            },
            methods : {
                set_max_date : function() {
                    let vm = this;
                    axios.post('/portal/leave/my-leave/get-date', {
                    // axios.get('/api/leave/application/get/date', {
                        params: { start_date: vm.mindate, allowed: {{$la->allowed}}, _token: vm.token }
                    }).then(function(response) {vm.maxdate = response.data;}).catch(function(error){ console.log(error); });
                }
            },
            created(){
                this.set_max_date();
                this.pval = '{{$leave->rstaff_id == null ? 30 : 75}}';
                if('{{$leave->status}}' === 'submitted') this.pval = 100;
                this.width = 'width: ' + this.pval + '%';
            }
        });
        $('.select').select2();
        $('.select-ns').select2({
            minimumResultsForSearch: Infinity,
        });

        @if(Laratrust::can('update-leave'))
        $(document).on('click', '.save-btn', function(e){

			e.preventDefault();

			var btn = $(this),
				btn_text = btn.html(),
				action = btn.data('action'),
				start_date = $("#start-date").val(),
				end_date = $("#end-date").val(),
				rstaff = $("#rstaff").val(),
				lcomment = $("#lcomment").val(),
				lid ='{{ Crypt::encrypt($leave->id) }}',
				token ='{{ Session::token() }}',
				url = "{{route('portal.leave.update', Crypt::encrypt($leave->id))}}";

			$.ajax({
				type: "POST",
				url: url,
				data: {
					start_date: start_date,
					end_date: end_date,
					rstaff: rstaff,
					action: action,
					comment: lcomment,
					lid: lid,
					_token: token
				},
				beforeSend: function () {
					btn.html('<i class="fas fa-spinner fa-spin"></i>');
                    $('#alertDiv').removeClass('alert alert-danger alert-success').html('');
				},
				success: function(response) {
                    btn.html(btn_text);
                    if(action === 'continue') {
                        $('#pbar').outerWidth('75%').html('75%');
                        palert('Leave application updated','success');
                        $('#leave-submit-tab').click();
                    } else {
                        $('#pbar').outerWidth('100%').html('100%');
                        window.location.href = "{{route('portal.leave')}}"
                    }
				},
				error: function(error){
					btn.html(btn_text);
                    palert(error,'error');
				}
			});
        });
        @endif

    });

</script>

@endsection
