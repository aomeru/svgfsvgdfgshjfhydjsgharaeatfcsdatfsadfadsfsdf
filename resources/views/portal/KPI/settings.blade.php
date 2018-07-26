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

    <div class="alert alert-dismissible fade show" :class="{ 'alert-danger' : perror, 'alert-success' : psuccess }" role="alert" v-if="perror || psuccess">
        <p class="mb-0" v-html="presponse"></p>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close" @click="unset_alert"><span aria-hidden="true">&times;</span></button>
    </div>

    <div class="row">
        <div :class="col_right">
            <div class="card">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Settings</h5>
                    @if(Laratrust::can('create-kpi-settings'))
                        <div class="d-flex justify-content-end" v-if="action == false">
                            <button class="btn btn-primary btn-sm no-margin" title="Add new user" @click="set_create"><i class="fa fa-plus"></i></button>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <div v-if="list.length == 0">
                        <p class="alert alert-info mb-0">No kpi settings found.</p>
                    </div>
                    <div v-else class="table-responsive">
                        <table class="table table-striped table-hover nowwrap data-table" width="100%" data-page-length="25">

                            <thead>
                                <tr class="active">
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Value</th>
                                    <th>Description</th>
                                    <th>Created By</th>
                                    @if(Laratrust::can('update-kpi-settings'))<th class="text-right">Actions</th>@endif
                                </tr>
                            </thead>

                            <tbody>

                                <tr v-for="(item, key) in list" data-title="@{!! item.title !!}" data-value="@{!! item.tvalue !!}">
                                    <td>@{{key + 1}}</td>
                                    <td class="text-capitalize">@{{item.title}}</td>
                                    <td>@{{item.tvalue}}</td>
                                    <td>@{{item.descrip}}</td>
                                    <td>@{{item.user.firstname + ' ' + item.user.lastname}}</td>
                                    @if(Laratrust::can('update-kpi-settings'))
                                        <td class="text-right">
                                            @if(Laratrust::can('update-kpi-settings'))
                                                <button class="btn btn-primary btn-sm" type="button" @click="set_edit(key)" :disabled="process_state"><i class="fas fa-pencil-alt"></i></button>
                                            @endif

                                            @if(Laratrust::can('delete-kpi-settings'))
                                                <button class="btn btn-danger btn-sm" type="button" @click="set_delete(key)" :disabled="process_state" v-if="item.title !== 'Appraisal Period'"><i class="fas fa-trash-alt"></i></button>
                                            @endif
                                        </td>
                                    @endif
                                </tr>

                            </tbody>

                        </table>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3" v-if="action">
            <div class="card">
                <div class="card-header text-white" :class="{  'bg-primary' : edit_state, 'bg-dark' : create_state, 'bg-danger' : delete_state }">
                    <h5 class="card-title mb-0 text-center">@{{action_title}}</h5>
                </div>

                @if(Laratrust::can('create-kpi-settings'))
                    <form @submit.prevent="store_process" v-if="create_state">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="stitle">Title</label>
                                <input type="text" id="stitle" class="form-control" placeholder="Enter setting title" v-model="stitle" :readonly="setap">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="setap" v-model="setap">
                                    <label class="custom-control-label font-weight-normal text-secondary" for="setap">Appraisal Period</label>
                                </div>
                            </div>

                            <div class="form-group mb-0" v-if="is_date">
                                <label for="date-value">Value</label>
                                <div class="input-group">
                                    <input id="date-value" type="date" class="form-control" placeholder="Select Date" min="{{date('Y-m-d')}}" v-model="date_value">
                                    <label for="date-value" class="input-group-append mb-0">
                                        <span class="input-group-text"><span class="fas fa-calendar"></span></span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group mb-0" v-else>
                                <label for="svalue">Value</label>
                                <input type="text" id="svalue" class="form-control" placeholder="Enter setting value" v-model="svalue" :hidden="is_date" :readonly="is_date">
                            </div>

                            <div class="custom-control custom-checkbox mb-3">
                                <input type="checkbox" class="custom-control-input" id="is-date" v-model="is_date">
                                <label class="custom-control-label font-weight-normal text-secondary" for="is-date">Input date value?</label>
                            </div>

                            <div class="form-group mb-0">
                                <label for="sdescrip">Description</label>
                                <textarea id="sdescrip" class="form-control" v-model="sdescrip"></textarea>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-sm-6">
                                    <button class="btn btn-block btn-success" type="submit" v-html="cbtn" :disabled="process_state"></button>
                                </div>
                                <div class="col-sm-6">
                                    <button class="btn btn-block btn-outline-secondary" type="button" @click="clear_action" :disabled="process_state"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>
                @endif

                @if(Laratrust::can('delete-kpi-settings'))
                    <div v-if="delete_state" class="text-center">
                        <div class="card-body">
                            <p class="mb-0">Are you sure you want to delete <span class="text-primary">"@{{list[key].title}}"</span> setting?</p>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-sm-6">
                                    <button class="btn btn-block btn-success" type="button" v-html="dbtn" :disabled="process_state" @click="delete_process"></button>
                                </div>
                                <div class="col-sm-6">
                                    <button class="btn btn-block btn-outline-secondary" type="button" @click="clear_action" :disabled="process_state"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(Laratrust::can('update-kpi-settings'))
                    <form @submit.prevent="update_process" v-if="edit_state">
                        <div class="card-body">
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
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-success" type="submit" v-html="sbtn" :disabled="process_state"></button>
                            <button class="btn btn-outline-secondary" type="button" @click="clear_action" :disabled="process_state"><i class="fas fa-times"></i></button>
                        </div>
                    </form>
                @endif
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
                    col_left : 'col-sm-3',
                    col_right : 'col-sm-12',
                    list : {!! $list->toJson() !!},
                    stitle : '',
                    svalue : '',
                    sdescrip : '',
                    key : null,
                    setap : false,
                    edit_item : {
                        title : '',
                        tvalue : '',
                        descrip : '',
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
                    is_date : false,
                    date_value : ''
                },
                methods : {
                    set_create : function(){
                        this.action = this.create_state = true
                        this.action_title = 'Create KPI Setting'
                    },
                    set_edit : function(key){
                        this.action = this.edit_state = true
                        this.edit_item.title = this.list[key].title
                        this.edit_item.tvalue = this.list[key].tvalue
                        this.edit_item.descrip = this.list[key].descrip
                        this.key = key
                        this.action_title = 'Edit KPI Setting: ' + this.edit_item.title
                    },
                    set_delete : function(key){
                        this.action = this.delete_state = true
                        this.action_title = 'Delete Setting'
                        this.key = key
                    },
                    clear_action : function(){
                        this.action = this.process_state = this.create_state = this.edit_state = this.delete_state = this.setap = false
                        this.action_title = this.stitle = this.svalue = this.sdescrip = this.key = this.edit_item.title = this.edit_item.tvalue = this.edit_item.descrip = ''
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
                        if(this.pmode === 'create') {
                            this.cbtn = this.button.create
                        } else if (this.pmode === 'edit') {
                            this.sbtn = this.button.save
                        } else this.dbtn = this.button.delete
                    },
                    unset_alert: function(){
                        this.psuccess = this.perror = false
                        this.presponse = ''
                    },
                    store_process : function(){
                        this.set_loading()
                        this.unset_alert()
                        let self = this
                        axios.post('/portal/kpi/settings/store', {
                            stitle : this.stitle,
                            svalue : this.svalue,
                            sdescrip : this.sdescrip,
                            setap : this.setap,
                        }).then((response) => {
                            self.unset_loading()
                            self.psuccess = true
                            self.presponse = response.data.msg
                            self.list = response.data.list
                            self.clear_action()
                        }).catch((error) => {
                            self.unset_loading()
                            self.perror = true
                            self.presponse = get_error_msg(error)
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
                    this.cbtn = this.button.create
                    this.sbtn = this.button.save
                    this.dbtn = this.button.delete
                },
                watch : {
                    action : function(){
                        if(this.action)
                        {
                            this.col_right = 'col-sm-9'
                        } else {
                            this.col_right = 'col-sm-12'
                        }
                    },
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
                    setap : function(x){
                        if(x)
                        {
                            this.stitle = 'Appraisal Period'
                        } else {
                            this.stitle = this.stitle.replace('Appraisal Period','')
                        }
                    },
                    date_value : function(x){
                        if(this.pmode === 'edit')
                        {
                            // if(x !== '') this.edit_item.tvalue = x
                        } else if(this.pmode === 'create'){
                            if(x !== '') this.svalue = x
                        }
                    },
                    is_date : function(x){
                        if(this.pmode === 'create')
                        {
                             if(!x) this.svalue = this.edit_item.tvalue = ''
                        }
                    }
                }
            });
        });

    </script>

@endsection
