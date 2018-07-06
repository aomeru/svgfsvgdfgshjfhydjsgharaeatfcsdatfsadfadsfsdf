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
use Illuminate\Support\Facades\Crypt;
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
        return view('portal.leave.allocation.index', [
			'list' => LeaveAllocation::whereHas('user', function($u){
                $u->orderBy('firstname');
            })->get(),
            'users' => User::orderBy('firstname')->with('leave_allocation')->get(),
            'ltypes' => LeaveType::select('id','title')->where('status','active')->orderBy('title')->get(),
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
                    $lt = LeaveType::where('title',$t)->where('status','active')->first();
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
        $id = Crypt::decrypt($id);
        $item = User::find($id);
        if($item == null){
            Session::flash('error','This employee leave allocation record does not exist, please confirm and try again');
            return redirect()->back();
        }
        $this->log(Auth::user()->id, 'Opened '.$item->firstname.' '.$item->lastname.' leave allocation page', Request()->path());
        return view('portal.leave.allocation.show', [
			'user' => $item,
			'las' => LeaveAllocation::where('user_id',$item->id)->get(),
            'nav' => 'leave',
			'subnav' => 'leave-allocation'
        ]);
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
    public function update(Request $r, $id)
    {
        $id = Crypt::decrypt($id);
        $item = LeaveAllocation::find($id);
        if($item == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['This leave allocation does not exist']]), 400);
        if($item->leave_type->status == 'inactive') return response()->json(array('success' => false, 'errors' => ['errors' => ['The leave type associated with this allocation is now inactive and can\'t be updated']]), 400);

        $rules = array(
            'val' => 'required|numeric',
        );
        $validator = Validator::make($r->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $item->allowed = round($r->val);
        if($item->update()) {
            $this->log(Auth::user()->id, 'Updated '.$item->user->fullname.' '.$item->leave_type->title.' leave allocation with id .'.$item->id, $r->path(),'action');
            return response()->json(array('success' => true, 'message' => 'Leave allocation updated'), 200);
        }
        return response()->json(array('success' => false, 'errors' => ['errors' => ['Oops, something went wrong please try again']]), 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = Crypt::decrypt($id);
		$item = LeaveAllocation::find($id);

        if($item == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['This leave allocation item does not exist.']]), 400);

        if($item->user->leave()->where('leave_type_id',$item->leave_type->id)->count() != 0) return response()->json(array('success' => false, 'errors' => ['errors' => ['This leave allocation item has application records and cannot be deleted.']]), 400);

        $did = $item->id;
        $user = User::find($item->user_id);

		if($item->delete()){
            $this->log(Auth::user()->id, 'Deleted leave allocation with id .'.$did.' for '.$user->fullname, Request()->path());
            return response()->json(array('success' => true, 'message' => 'Leave allocation deleted for '.$user->fullname), 200);
        }

		return response()->json(array('success' => false, 'errors' => ['errors' => ['Oops, something went wrong please try again']]), 400);
    }

    public function to_users(Request $r)
    {
        $rules = array(
            'etype' => 'required|in:All,Full Time,Contract,Part Time,Graduate Trainee',
            'ltype' => 'required|exists:leave_type,title',
            'update_mode' => 'required|in:reset,add',
        );
        $validator = Validator::make($r->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }
        $x= 0;
        $users = $r->etype == 'All' ? User::all() : User::where('employee_type',$r->etype)->get();
        foreach($users as $user)
        {
            $lt = LeaveType::where('title',$r->ltype)->where('status','active')->first();
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
                        $x++;
                        $this->log(Auth::user()->id, 'Allocated '.$lt->title.' leave type to '.$user->firstname.' '.$user->lastname, $r->path(),'action');
                    }
                } else {
                    if($lt->type == 'static')
                    {
                        $rec->allowed = $r->update_mode == 'reset' ? $lt->allowed : $rec->allowed + $lt->allowed;
                    } else {
                        $then = new DateTime($user->date_of_hire);
                        $now = new DateTime();
                        $months = $then->diff($now)->m + ($then->diff($now)->y*12);
                        $allowed = $months * $lt->allowed;
                        $rec->allowed = $r->update_mode == 'reset' ? $allowed : $rec->allowed + $allowed;
                    }
                    if($rec->update())
                    {
                        $x++;
                        $this->log(Auth::user()->id, 'Updated '.$lt->title.' leave type allocation for '.$user->firstname.' '.$user->lastname, $r->path(),'action');
                    }
                }
            }
        }

        $msg = $x == $users->count() ? 'Leave types allocated to the selected employees' : 'Some leave types could not be allocated to some employees';

        return response()->json(array('success' => true, 'message' => $msg), 200);

		return response()->json(array('success' => false, 'errors' => ['errors' => ['Oops, something went wrong please try again']]), 400);
    }
}
