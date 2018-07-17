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
        <div class="card">

            <div class="card-body py-5">

                <form @submit.prevent="process_form">
                    <div class="row">
                        <div class="col-sm-6 offset-sm-3">

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="start-date" class="form-control-label">Start Date</label>
                                        <div class="input-group">
                                            <input id="start-date" type="date" class="form-control" placeholder="Start Date" v-model="start_date" min="{{ date('Y-m-d') }}">
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
                                            <input id="end-date" type="date" class="form-control" placeholder="End Date" :min="start_date" :max="max_date" v-model="end_date">
                                            <label for="end-date" class="input-group-append mb-0">
                                                <span class="input-group-text"><span class="fas fa-calendar"></span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="rstaff" class="form-control-label">Relieving Staff</label>
                                <select id="rstaff" class="custom-select" style="width: 100%;" v-model="rstaff">
                                    <option value="">Select Staff</option>
                                    @foreach($col as $c)
                                        <option value="{{$c->email}}" @if($leave->rstaff == $c->id) selected @endif>{{$c->fullname}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <textarea name="lcomment" id="lcomment" class="form-control" rows="5" placeholder="Additional Information?" v-model="comment"></textarea>
                            </div>
                            <hr class="my-3">

                            <p class="text-muted">
                                Your application will be submitted to your manager "<span class="text-primary">{{Auth::user()->manager == null ? 'No Manager':  Auth::user()->manager->manager->fullname}}</span>" for approval
                                <br>
                                <small>Please contact HR if your manager information is incorrect</small>
                            </p>

                            <div class="alert alert-danger alert-dismissible fade show" role="alert" v-if="perror">
                                <p class="mb-0" v-html="presponse"></p>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close" @click="unset_alert"><span aria-hidden="true">&times;</span></button>
                            </div>

                            <button class="btn-success btn save-btn mr-2" type="submit" role="button" @if(Auth::user()->manager == null) disabled @endif v-html="sbtn"></button>

                            <a class="btn-white btn" href="{{route('portal.leave')}}"><i class="fas fa-times mr-2"></i>Cancel</a>
                        </div>
                    </div>
                </form>

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
        @if(Laratrust::can('update-leave'))
        var app = new Vue({
            el : '#portal',
            data : {
                start_date : '{{$leave->start_date}}',
                end_date : '{{$leave->end_date}}',
                rstaff : '{{$leave->ruser->email}}',
                comment : '{{$leave->comment}}',
                max_date : '',
                perror : false,
                presponse : '',
                button : {
                    loading : '<i class="fas fa-spinner fa-spin"></i>',
                    submit : '<i class="fas fa-check mr-2"></i>Submit',
                },
                sbtn : '',
            },
            methods : {
                set_loading : function () {
                    this.sbtn = this.button.loading
                },
                unset_loading : function () {
                    this.sbtn = this.button.submit
                },
                unset_alert: function(){
                    this.perror = false
                    this.presponse = ''
                },
                set_max_date : function(){
                    if(this.start_date !== '')
                    {
                        let self = this
                        axios.post('/portal/leave/my-leave/get-date', {
                            start_date : this.start_date,
                            ltype : '{{encrypt($la->id)}}',
                        }).then((response) => {
                            self.max_date = response.data
                        }).catch((error) => {
                            console.log(error)
                        });
                    }
                },
                process_form : function(){
                    this.set_loading()
                    this.unset_alert()
                    let self = this
                    axios.post('/portal/leave/my-leave/update',{
                        id : '{{encrypt($leave->id)}}',
                        start_date : this.start_date,
                        end_date : this.end_date,
                        rstaff : this.rstaff,
                        comment : this.comment,
                    }).then((response)=>{
                        window.location.href = "{{route('portal.leave')}}"
                    }).catch((error)=>{
                        console.log(error);
                        self.unset_loading()
                        self.perror = true
                        self.presponse = get_error_msg(error)
                    })
                },
            },
            watch : {
                start_date : function(){
                    this.set_max_date()
                },
            },
            created(){
                this.sbtn = this.button.submit
                this.set_max_date()
            },
            mounted : function(){
                $('.select').select2();
                $('.select-ns').select2({
                    minimumResultsForSearch: Infinity,
                });
            }
        });
        @endif

    });

</script>

@endsection
