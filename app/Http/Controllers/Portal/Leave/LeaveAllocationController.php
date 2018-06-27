<?php

namespace App\Http\Controllers\Portal\Leave;

use App\User;
use DateTime;
use Carbon\Carbon;
use App\Models\LeaveType;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use App\Models\LeaveAllocation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
    public function store(Request $r)
    {
        $x = 0;
        $y = 0;
        foreach($r->users as $e)
        {
            $user = User::where('email',$e)->first();
            if($user != null)
            {
                foreach($r->types as $t)
                {
                    $lt = LeaveType::where('title',$t)->first();
                    if($lt != null)
                    {
                        $rec = LeaveAllocation::where('user_id',$user->id)->where('leave_type_id',$lt->id)->first();
                        if($rec == null)
                        {
                            $all = new LeaveAllocation;
                            $all->user_id = $user->id;
                            $all->leave_type_id = $lt->id;
                            $all->year = substr($lt->title,-4);
                            if($lt->type == 'static') $all->allowed = $lt->allowed; else {
                                $then = new DateTime($user->date_of_hire);
                                $now = new DateTime();
                                $months = $then->diff($now)->m + ($then->diff($now)->y*12);
                                $all->allowed = $months * $lt->allowed;
                            }
                            if($all->save())
                            {
                                $x++; $y++;
                                $this->log(Auth::user()->id, 'Allocated '.$lt->title.' leave type to '.$user->firstname.' '.$user->lastname, $r->path(),'action');
                            }
                        }
                    }
                }
            }
        }
        if($x == count($r->users) && $y == count($r->types)) $msg = 'Leave types allocated to the selected employees'; else $msg = 'Some leave types could not be allocated to some employees';

        return response()->json(array('success' => true, 'message' => $msg), 200);

		return response()->json(array('success' => false, 'errors' => ['errors' => ['Oops, something went wrong please try again']]), 400);
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
