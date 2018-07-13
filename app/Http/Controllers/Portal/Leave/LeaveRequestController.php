<?php

namespace App\Http\Controllers\Portal\Leave;

use App\Role;
use App\User;
use Carbon\Carbon;
use App\Traits\LeaveTrait;
use App\Traits\CommonTrait;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Notifications\GeneralNotification;

class LeaveRequestController extends Controller
{
    use CommonTrait;
    use LeaveTrait;

    public function __construct()
    {
        $this->middleware('role:manager|general-manager|executive-director|leave-manager');
        $this->middleware('permission:update-leave-request', ['only' => ['show','update']]);
        $this->middleware('permission:approve-decline-leave', ['only' => ['manager_action']]);
    }

    public function index()
    {
        $this->log(Auth::user()->id, 'Opened the my leave requests page.', Request()->path());
        return view('portal.leave.request.index', [
			'list' => LeaveRequest::all(),
            'nav' => 'leave',
			'subnav' => 'leave-requests',
		]);
    }

    public function show($code)
    {
        $item = LeaveRequest::where('code',$code)->first();
        if($item == null) { Session::flash('error','This leave request record does not exists'); return redirect()->back(); }
        // check manager
        // check permission
        // dd($item->log);
        // check if start date elapsed
        $role = Role::where('display_name','Leave Manager')->first();
        $this->log(Auth::user()->id, 'Opened the leave request '.$item->code.' page.', Request()->path());
        return view('portal.leave.request.show', [
			'item' => $item,
			'lms' => $role->users,
            'nav' => 'leave',
			'subnav' => 'leave-requests',
		]);
    }

    public function manager_action(Request $r)
    {
        // return response()->json($r->all(),200);

        $item = LeaveRequest::where('code',$r->code)->first();
        if($item == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['This leave application was not found']]), 422);

        if(in_array($r->pmode, ['approve','defer']))
        {
            $validator = Validator::make($r->all(), ['hr' => 'required|exists:users,email'], ['hr.required'=>'Please select an HR staff to process this request.','hr.exists'=>'Please select an HR staff to process this request.']);
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 400);
            }
        }
        $hr_staff = User::where('email',$r->hr)->first();

        switch($r->pmode){
            case 'approve':
                $item->manager_decision = 'manager_approved';
                $item->update([
                    'manager_decision_date' => Carbon::now(),
                    'hr_id' => $hr_staff->id,
                    'status' => 'manager_approved',
                ]);
                $item->leave()->update(['status' => 'manager_approved']);
                if($r->comment != null)
                {
                    $item->log()->create([
                        'type' => 'comment',
                        'user_id' => Auth::user()->id,
                        'comment' => $r->comment,
                    ]);
                }
                $item->log()->create([
                    'comment' => 'Manager approved leave request',
                ]);
                $this->log(Auth::user()->id, 'Manager carried out '.$r->pmode.' action on leave request '.$item->code, Request()->path());
                $item->hr->notify(new GeneralNotification([
                    'title' => $item->leave->user->fullname.'\'s leave request awaiting your approval',
                    'url' => route('portal.leave.request.show',$item->code),
                ]));
                $item->leave->user->notify(new GeneralNotification([
                    'title' => $item->code.' approved by manager, leave request awaiting HR approval',
                    'url' => route('portal.leave.request.show',$item->code),
                ]));
                Session::flash('success',$item->code.' leave request approved and submitted to HR for further action');
                return response()->json(['Leave request approved'],200);
                // set log
                // send response
                break;
        }
    }

    protected function set_defer($s,$e)
    {

    }
}
