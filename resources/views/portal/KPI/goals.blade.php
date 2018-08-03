@extends('layouts.portal')
@section('page_title','KPI Goals - ')
@section('portal_page_title') <i class="fas fa-chart-line mr-3"></i>KPI Goals @endSection

@section('bc')
    <nav aria-label="breadcrumb" class="d-none d-md-block">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('portal')}}">Dashboard</a></li>
            <li class="breadcrumb-item">KPI</li>
            <li class="breadcrumb-item active" aria-current="page">Goals</li>
        </ol>
    </nav>
@endSection

@section('content')

    <div id="presponse" class="alert alert-dismissible fade show" :class="{ 'alert-danger' : perror, 'alert-success' : psuccess }" role="alert" v-if="perror || psuccess">
        <p class="mb-0" v-html="presponse"></p>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close" @click="unset_alert"><span aria-hidden="true">&times;</span></button>
    </div>

    <div class="card">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Key Performance Indicators</h5>
            @if(Laratrust::can('create-kpi-goals') && $cm)
                <div class="d-flex justify-content-end" v-if="action == false">
                    <button class="btn btn-primary btn-sm no-margin" title="Create new KPI goal" @click="set_create"><i class="fa fa-plus"></i></button>
                </div>
            @endif
        </div>

        <div class="card-body py-2 bg-info text-white">
            <p class="mb-0 text-right text-light">
                Appraisal Period: <span class="text-white font-weight-bold">{{$ap}}</span>
            </p>
        </div>

        <div class="card-body border-bottom" v-if="action">
            <h4 class="card-title" :class="{ 'text-primary' : edit_state, 'text-danger' : delete_state }">@{{action_title}}</h4>

            @if(Laratrust::can('create-kpi-goals'))
                <form @submit.prevent="store_process" v-if="create_state">
                    <div class="row">
                        <div class="col-sm-8">
                            <div class="form-group">
                                <label for="goal">Goal</label>
                                <textarea id="goal" class="form-control" v-model="goal"></textarea>
                                <div class="custom-control custom-checkbox mt-1">
                                    <input type="checkbox" class="custom-control-input" id="is-sub-goal" v-model="is_sub_goal">
                                    <label class="custom-control-label font-weight-normal text-secondary" for="is-sub-goal">This is a  Sub Goal?</label>
                                </div>
                            </div>
                            <div class="form-group mt-1" v-if="is_sub_goal">
                                <label for="parent-goal">Parent Goal</label>
                                <select id="parent-goal" class="custom-select" v-model="parent_id">
                                    <option value="">Select Parent Goal</option>
                                    <option v-for="pgoal in list[0].goals" :value="pgoal.id">@{{pgoal.goal}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="goal-weight">Goal Weight</label>
                                <select id="goal-weight" class="custom-select" v-model="weight">
                                    <option value="">Select Weight</option>
                                    <option v-for="val in weight_limit" :value="val">@{{val}}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-success mr-2" type="submit" v-html="cbtn" :disabled="process_state"></button>
                    <button class="btn btn-outline-secondary" type="button" @click="clear_action" :disabled="process_state"><i class="fas fa-times"></i></button>

                </form>
            @endif

            @if(Laratrust::can('delete-kpi-goals'))
                <div v-if="delete_state" class="text-center">
                    <p class="mb-0">Are you sure you want to delete <span class="text-primary">"@{{list[key].title}}"</span> setting?</p>
                    <div class="row">
                        <div class="col-sm-6">
                            <button class="btn btn-block btn-success" type="button" v-html="dbtn" :disabled="process_state" @click="delete_process"></button>
                        </div>
                        <div class="col-sm-6">
                            <button class="btn btn-block btn-outline-secondary" type="button" @click="clear_action" :disabled="process_state"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                </div>
            @endif

            @if(Laratrust::can('update-kpi-goals'))
                <form @submit.prevent="update_process" v-if="edit_state">
                    <div class="form-group">
                        <label for="stitle-edit">Title</label>
                        <input type="text" id="stitle-edit" class="form-control" placeholder="Enter setting title" v-model="edit_item.title" :readonly="edit_item.title === 'Appraisal Period'">
                    </div>

                    <div class="form-group" v-if="is_date">
                        <label for="date-value-edit">Value</label>
                        <div class="input-group">
                            <input id="date-value-edit" type="date" class="form-control" placeholder="Select Date" min="{{date('Y-m-d')}}" v-model="date_value">
                            <label for="date-value-edit" class="input-group-append mb-0">
                                <span class="input-group-text"><span class="fas fa-calendar"></span></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group" v-else :class="{ 'mb-0' : edit_item.title !== 'Appraisal Period' }">
                        <label for="svalue-edit">Value</label>
                        <input type="text" id="svalue-edit" class="form-control" placeholder="Enter setting value" v-model="edit_item.tvalue" :hidden="is_date" :readonly="is_date">
                    </div>

                    <div class="custom-control custom-checkbox mb-3" v-show="edit_item.title !== 'Appraisal Period'">
                        <input type="checkbox" class="custom-control-input" id="is-date" v-model="is_date">
                        <label class="custom-control-label font-weight-normal text-secondary" for="is-date">Input date value?</label>
                    </div>

                    <div class="form-group mb-0">
                        <label for="sdescrip-edit">Description</label>
                        <textarea id="sdescrip-edit" class="form-control" v-model="edit_item.descrip"></textarea>
                    </div>
                    <button class="btn btn-success" type="submit" v-html="sbtn" :disabled="process_state"></button>
                    <button class="btn btn-outline-secondary" type="button" @click="clear_action" :disabled="process_state"><i class="fas fa-times"></i></button>
                </form>
            @endif
        </div>

        <div class="card-body">

            <div v-if="list.length == 0">
                <p class="alert alert-info mb-0">No kpi goals found.</p>
            </div>

            <div v-else>
                <nav>
                    <div class="nav nav-tabs" id="kpi-nav-tab" role="tablist">
                        <a v-for="(kpi, key) in list" class="nav-item nav-link" :class="{ active : key === 0 }" :id="set_nt_id(kpi.appraisal_period)" data-toggle="tab" :href="set_nc_id(kpi.appraisal_period,'#')" role="tab" :aria-controls="set_nc_id(kpi.appraisal_period)" aria-selected="true">@{{kpi.appraisal_period}}</a>
                    </div>
                </nav>

                <div class="tab-content p-3 border border-top-0" id="kpi-nav-tabContent">
                    <div v-for="(kpi, key) in list" class="tab-pane fade" :class="{ 'show active' : key === 0 }" :id="set_nc_id(kpi.appraisal_period)" role="tabpanel" :aria-labelledby="set_nt_id(kpi.appraisal_period)">

                        <div class="table-responsive">
                            <table class="table table-stripedd table-hover nowwrap data-table" width="100%" data-page-length="25">

                                <thead>
                                    <tr class="active">
                                        <th>#</th>
                                        <th>Goal</th>
                                        <th class="text-center">Weight</th>
                                        <th class="text-center" title="Sub goals">SG</th>
                                        @if(Laratrust::can('update-kpi-goals'))<th class="text-right">Actions</th>@endif
                                    </tr>
                                </thead>

                                <tbody>

                                    <tr v-for="(goal, gkey) in kpi.goals">
                                        <td>@{{gkey + 1}}</td>
                                        <td>@{{goal.goal}}</td>
                                        <td class="text-center">@{{goal.weight}}</td>
                                        <td class="text-center">@{{goal.goals.length}}</td>
                                        @if(Laratrust::can('update-kpi-goals'))
                                            <td class="text-right">
                                                @if(Laratrust::can('update-kpi-goals'))
                                                    <button class="btn btn-primary btn-sm" type="button" @click="set_edit(gkey)" :disabled="process_state"><i class="fas fa-pencil-alt"></i></button>
                                                @endif

                                                @if(Laratrust::can('delete-kpi-goals'))
                                                    <button class="btn btn-danger btn-sm" type="button" @click="set_delete(gkey)" :disabled="process_state"><i class="fas fa-trash-alt"></i></button>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>


                                    {{-- <tr v-for="(sgoal, sgkey) in kpi.goals" v-if="kpi.goals.goals !== null">
                                        <td>@{{gkey + 1 + '.'}}</td>
                                        <td>@{{goal.goal}}</td>
                                        <td class="text-center">@{{goal.weight}}</td>
                                        @if(Laratrust::can('update-kpi-goals'))
                                            <td class="text-right">
                                                @if(Laratrust::can('update-kpi-goals'))
                                                    <button class="btn btn-primary btn-sm" type="button" @click="set_edit(gkey)" :disabled="process_state"><i class="fas fa-pencil-alt"></i></button>
                                                @endif

                                                @if(Laratrust::can('delete-kpi-goals'))
                                                    <button class="btn btn-danger btn-sm" type="button" @click="set_delete(gkey)" :disabled="process_state"><i class="fas fa-trash-alt"></i></button>
                                                @endif
                                            </td>
                                        @endif
                                    </tr> --}}


                                </tbody>

                            </table>

                        </div>
                    </div>
                </div>


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
                    pmode : '',
                    action : false,
                    action_title : '',

                    edit_state : false,
                    create_state : false,
                    delete_state : false,
                    process_state : false,

                    list : {!! $list->toJson() !!},
                    weight_limit : 50,

                    goal : '',
                    weight : '',
                    parent_id : '',
                    is_sub_goal : false,

                    key : null,
                    edit_item : {
                        goal : '',
                        weight : '',
                        parent_id : '',
                        is_sub_goal : false,
                    },

                    button : {
                        loading : '<i class="fas fa-spinner fa-spin"></i>',
                        create : '<i class="fas fa-check mr-2"></i>Create',
                        save : '<i class="fas fa-check mr-2"></i>Save Changes',
                        delete : '<i class="fas fa-trash-alt mr-2"></i>Yes, delete',
                    },
                    cbtn : '',
                    sbtn : '',
                    dbtn : '',
                    psuccess : false,
                    perror : false,
                    presponse : '',
                },
                methods : {
                    set_nt_id : function(v){
                        return 'nav-' + get_slug(v) + '-tab'
                    },
                    set_nc_id : function(v,h = ''){
                        return h + 'nav-' + get_slug(v) + '-content'
                    },
                    // set_encrypt : function(v){
                    //     console.log({{ encrypt(' + v + ') }});
                    // },
                    set_create : function(){
                        this.action = this.create_state = true
                        this.action_title = 'Create KPI Goal'
                    },
                    set_edit : function(key){
                        this.action = this.edit_state = true
                        this.edit_item.goal = this.list[key].goal
                        this.edit_item.weight = this.list[key].weight
                        this.edit_item.parent_id = this.list[key].parent_id === null ? '' : this.list[key].parent_id
                        this.edit_item.is_sub_goal = this.list[key].parent_id === null ? false : true
                        this.key = key
                        this.action_title = 'Edit KPI Goal'
                    },
                    set_delete : function(key){
                        this.action = this.delete_state = true
                        this.action_title = 'Delete KPI Goal'
                        this.key = key
                    },
                    clear_action : function(){
                        this.action = this.process_state = this.create_state = this.edit_state = this.delete_state = this.setap = this.is_sub_goal = this.edit_item.goal = false
                        this.action_title = this.goal = this.weight = this.parent_id = this.key = this.edit_item.goal = this.edit_item.weight = this.edit_item.parent_id = ''
                    },
                    set_loading : function () {
                        this.process_state = true;
                        if(this.pmode === 'create') {
                            this.cbtn = this.button.loading
                        } else if (this.pmode === 'edit') {
                            this.sbtn = this.button.loading
                        } else this.dbtn = this.button.loading
                    },
                    unset_loading : function () {
                        this.process_state = false;
                        this.cbtn = this.button.create
                        this.sbtn = this.button.save
                        this.dbtn = this.button.delete
                    },
                    unset_alert: function(){
                        this.psuccess = this.perror = false
                        this.presponse = ''
                    },
                    store_process : function(){
                        this.set_loading()
                        this.unset_alert()
                        let self = this
                        axios.post('/portal/kpi/goals/store', {
                            goal : this.goal,
                            weight : this.weight,
                            is_sub_goal : this.is_sub_goal,
                            parent_id : this.parent_id,
                        }).then((response) => {
                            self.unset_loading()
                            self.psuccess = true
                            self.presponse = response.data.msg
                            self.list = response.data.list
                            self.clear_action()
                            location.href = "#portal-right";
                        }).catch((error) => {
                            self.unset_loading()
                            self.perror = true
                            self.presponse = get_error_msg(error)
                            location.href = "#portal-right";
                        });
                    },
                    update_process : function(){
                        this.set_loading()
                        this.unset_alert()
                        let self = this
                        axios.post('/portal/kpi/settings/update', {
                            title : this.list[this.key].title,
                            stitle : this.edit_item.title,
                            svalue : this.edit_item.tvalue,
                            sdescrip : this.edit_item.descrip,
                        }).then((response) => {
                            self.unset_loading()
                            self.psuccess = true
                            self.presponse = response.data[0]
                            this.list[this.key].title = this.edit_item.title
                            this.list[this.key].tvalue = this.edit_item.tvalue
                            this.list[this.key].descrip = this.edit_item.descrip
                            self.clear_action()
                        }).catch((error) => {
                            self.unset_loading()
                            self.perror = true
                            self.presponse = get_error_msg(error)
                        });
                    },
                    delete_process : function(){
                        this.set_loading()
                        this.unset_alert()
                        let self = this
                        axios.get('/portal/kpi/settings/' + this.list[this.key].title + '/delete').then((response) => {
                            this.list.splice(this.key,1)
                            self.unset_loading()
                            self.psuccess = true
                            self.presponse = response.data[0]
                            // self.list = response.data.list
                            self.clear_action()
                            // window.location.href = "{{route('portal.kpi.settings')}}"
                        }).catch((error) => {
                            self.unset_loading()
                            self.perror = true
                            self.presponse = get_error_msg(error)
                        });
                    }
                },
                mounted(){
                    $('.data-table').DataTable();
                },
                created(){
                    // this.set_create()
                    this.cbtn = this.button.create
                    this.sbtn = this.button.save
                    this.dbtn = this.button.delete
                },
                watch : {
                    create_state : function(x){
                        this.pmode = x ? 'create' : ''
                        this.edit_state = this.delete_state = false
                    },
                    edit_state : function(x){
                        this.pmode = x ? 'edit' : ''
                        this.create_state = this.delete_state = false
                    },
                    delete_state : function(x){
                        this.pmode = x ? 'delete' : ''
                        this.edit_state = this.create_state = false
                    },
                    // is_date : function(x){
                    //     if(this.pmode === 'create')
                    //     {
                    //          if(!x) this.svalue = this.edit_item.tvalue = ''
                    //     }
                    // }
                }
            });
        });

    </script>

@endsection
