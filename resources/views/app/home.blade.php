@extends('layouts.app')
@section('page_title','Welcome to your ')

@section('content')
<div class="container">
    @include('partials.messages')
    @if(session()->has('userinfo'))
    <div class="row justify-content-end mb-3">
        <div class="col-sm-6">
            <div class="d-flex justify-content-end">
                <div class="d-flex align-items-center">
                    <div class="">
                        <h4 class="mb-0">
                            {{session('userinfo.display_name')}}
                        </h4>
                        <p class="mb-0 c-666">
                            <em>{{session('userinfo.job_title') ? session('userinfo.job_title') : 'Job Title'}} / {{session('userinfo.dept') ? session('userinfo.dept') : 'Department'}}</em>
                        </p>
                    </div>
                </div>

                <div class="ml-3 d-flex align-items-center" style="height: 70px">
                    <img src="@if(!session('userinfo.photo')) {{ asset('images/user.png') }} @else data:image.jpg;base64,{{session('userinfo.photo')}} @endif" class="img-fluuid rounded-circle border border-white" alt="" width="auto" height="100%">
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="jumbotron bg-white">
        <h1 class="display-4">Welcome to the ERP Portal</h1>
    </div>
</div>
@endsection
