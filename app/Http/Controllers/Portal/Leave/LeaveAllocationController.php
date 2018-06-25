<?php

namespace App\Http\Controllers\Portal\Leave;

use App\User;
use App\Models\LeaveType;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use App\Models\LeaveAllocation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LeaveAllocationController extends Controller
{
    use CommonTrait;

    public function __construct()
    {
        $this->middleware('permission:create-leave-allocation', ['only' => ['store']]);
        $this->middleware('permission:read-leave-allocation');
        $this->middleware('permission:update-leave-allocation', ['only' => ['update']]);
        $this->middleware('permission:delete-leave-allocation', ['only' => ['delete']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->log(Auth::user()->id, 'Opened the leave type page.', Request()->path());
        return view('portal.leave.allocation', [
			'list' => LeaveAllocation::whereHas('user', function($u){
                $u->orderBy('firstname');
            })->get(),
            'users' => User::orderBy('firstname')->with('leave_allocation')->get(),
            'ltypes' => LeaveType::select('id','title')->orderBy('title')->get(),
            'nav' => 'leave',
			'subnav' => 'leave-allocation',
		]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
