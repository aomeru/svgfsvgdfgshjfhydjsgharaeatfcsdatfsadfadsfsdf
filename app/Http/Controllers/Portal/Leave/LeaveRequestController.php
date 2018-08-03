<?php

namespace App\Http\Controllers\Portal\Leave;

use App\Role;
use App\User;
use Carbon\Carbon;
use Laratrust\Laratrust;
use App\Traits\LeaveTrait;
use App\Traits\CommonTrait;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use App\Http\Requests\DeferRequest;
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
        // $this->middleware('role:system-administrator|manager|general-manager|executive-director|leave-manager');
        $this->middleware('permission:read-leave-request');
        $this->middleware('permission:read-leave-request|update-leave-request', ['only' => ['show','update']]);
        $this->middleware('permission:approve-decline-leave', ['only' => ['manager_action','hr_action']]);
    }

    public function index()
    {
        $this->log(Auth::user()->id, 'Opened the my leave requests page.', Request()->path());
        return view('portal.leave.request.index', [
			'list' => Auth::user()->hasRole(['leave-manager','system-administrator']) ? LeaveRequest::all() : LeaveRequest::where('manager_id',Auth::id())->orWhere('hr_id',Auth::id())->get(),
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

    public function manager_action(DeferRequest $r)
    {
        // return response()->json($r->all(),200);

        $item = LeaveRequest::where('code',$r->code)->first();

        if(in_array($r->pmode, ['approve','defer']))
        {
            $validator = Validator::make($r->all(), ['hr' => 'required|exists:users,email'], ['hr.required'=>'Please select an HR staff to process this request.','hr.exists'=>'Please select an HR staff to process this request.']);
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }
            $hr_staff = User::where('email',$r->hr)->first();
        }

        switch($r->pmode){
            case 'approve':
                $item->update([
                    'manager_decision' => 'manager_approved',
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
                    'title' => $item->code.' leave request approved by manager and awaiting HR approval',
                    'url' => route('portal.leave.request.show',$item->code),
                ]));
                Session::flash('success',$item->code.' leave request approved and submitted to HR for further action');
                return response()->json(['Leave request approved'],200);
                break;



            case 'decline':
                $item->update([
                    'manager_decision' => 'manager_declined',
                    'manager_decision_date' => Carbon::now(),
                    'status' => 'manager_declined',
                ]);
                $item->leave()->update(['status' => 'manager_declined']);
                if($r->comment != null)
                {
                    $item->log()->create([
                        'type' => 'comment',
                        'user_id' => Auth::user()->id,
                        'comment' => $r->comment,
                    ]);
                }
                $item->log()->create([
                    'comment' => 'Manager declined leave request',
                ]);
                $this->log(Auth::user()->id, 'Manager carried out '.$r->pmode.' action on leave request '.$item->code, Request()->path());
                $item->leave->user->notify(new GeneralNotification([
                    'title' => $item->code.' leave request declined by your manager',
                    'url' => route('portal.leave.request.show',$item->code),
                ]));
                Session::flash('success',$item->code.' leave request declined');
                return response()->json(['Leave request declined'],200);
                break;



            case 'defer':
                $a = $this->leave_dates($r->start_date, $r->end_date);
                $la = $item->leave->user->leave_allocation()->where('leave_type_id',$item->leave->leave_type_id)->first();
                if($a['no_days'] > $la->allowed) return response()->json(['errors' => ['error' => ['You have selected more days over user allocation for this leave type.']]], 422);

                $item->update([
                    'manager_decision' => 'manager_deferred',
                    'manager_decision_date' => Carbon::now(),
                    'hr_id' => $hr_staff->id,
                    'status' => 'manager_deferred',
                ]);
                $item->leave()->update(['status' => 'manager_deferred']);

                if($item->deference == null)
                {
                    $item->deference()->create([
                        'type' => 'manager',
                        'start_date' => $a['sd'],
                        'end_date' => $a['ed'],
                        'back_on' => $a['bd'],
                        'comment' => $r->comment
                    ]);
                } else {
                    $item->deference()->update([
                        'start_date' => $a['sd'],
                        'end_date' => $a['ed'],
                        'back_on' => $a['bd'],
                        'comment' => $r->comment
                    ]);
                }
                if($r->comment != null)
                {
                    $item->log()->create([
                        'type' => 'comment',
                        'user_id' => Auth::user()->id,
                        'comment' => $r->comment,
                    ]);
                }
                $item->log()->create(['comment' => 'Manager approved leave request with deference']);
                $this->log(Auth::user()->id, 'Manager carried out '.$r->pmode.' action on leave request '.$item->code, Request()->path());
                $item->hr->notify(new GeneralNotification([
                    'title' => $item->leave->user->fullname.'\'s leave request awaiting your approval',
                    'url' => route('portal.leave.request.show',$item->code),
                ]));
                $item->leave->user->notify(new GeneralNotification([
                    'title' => $item->code.' leave request approved by manager with a deference condition and awaiting HR approval',
                    'url' => route('portal.leave.request.show',$item->code),
                ]));
                Session::flash('success',$item->code.' leave request approved with deference');
                return response()->json(['Leave request approved with deference'],200);
                break;
        }
    }

    public function hr_action(DeferRequest $r)
    {
        $item = LeaveRequest::where('code',$r->code)->first();
        switch($r->pmode){
            case 'approve':
                $item->update([
                    'hr_decision' => 'hr_approved',
                    'hr_decision_date' => Carbon::now(),
                    'status' => 'hr_approved',
                ]);
                $item->leave()->update(['status' => 'hr_approved']);
                if($item->deference != null)
                {
                    $item->leave()->update([
                        'start_date' => $item->deference->start_date,
                        'end_date' => $item->deference->end_date,
                        'back_on' => $item->deference->back_on,
                    ]);
                }
                $this->deduct_all($item);
                if($r->comment != null)
                {
                    $item->log()->create([
                        'type' => 'comment',
                        'user_id' => Auth::user()->id,
                        'comment' => $r->comment,
                    ]);
                }
                $item->log()->create([
                    'comment' => 'HR approved leave request',
                ]);
                $this->log(Auth::user()->id, 'HR carried out '.$r->pmode.' action on leave request '.$item->code, Request()->path());
                $item->leave->user->notify(new GeneralNotification([
                    'title' => $item->code.' leave request has been approved',
                    'url' => route('portal.leave.request.show',$item->code),
                ]));
                $item->leave->ruser->notify(new GeneralNotification([
                    'title' => $item->leave->user->fullname.' leave request has been approved, employee\'s duties have been reassigned to you from '.$item->leave->start_date.' to '.$item->leave->end_date,
                    'url' => '',
                ]));
                Session::flash('success',$item->code.' leave request approved');
                return response()->json(['Leave request approved'],200);
                break;



            case 'decline':
                $item->update([
                    'hr_decision' => 'hr_declined',
                    'hr_decision_date' => Carbon::now(),
                    'status' => 'hr_declined',
                ]);
                $item->leave()->update(['status' => 'hr_declined']);
                if($r->comment != null)
                {
                    $item->log()->create([
                        'type' => 'comment',
                        'user_id' => Auth::user()->id,
                        'comment' => $r->comment,
                    ]);
                }
                $item->log()->create([
                    'comment' => 'HR declined leave request',
                ]);
                $this->log(Auth::user()->id, 'HR carried out '.$r->pmode.' action on leave request '.$item->code, Request()->path());
                $item->leave->user->notify(new GeneralNotification([
                    'title' => $item->code.' leave request declined by HR',
                    'url' => route('portal.leave.request.show',$item->code),
                ]));
                Session::flash('success',$item->code.' leave request declined');
                return response()->json(['Leave request declined'],200);
                break;



            case 'defer':
                $a = $this->leave_dates($r->start_date, $r->end_date);
                $la = $item->leave->user->leave_allocation()->where('leave_type_id',$item->leave->leave_type_id)->first();
                if($a['no_days'] > $la->allowed) return response()->json(['errors' => ['error' => ['You have selected more days over user allocation for this leave type.']]], 422);

                $item->update([
                    'hr_decision' => 'hr_deferred',
                    'hr_decision_date' => Carbon::now(),
                    'status' => 'hr_deferred',
                ]);
                $item->leave()->update(['status' => 'hr_deferred']);

                if($item->deference == null)
                {
                    $item->deference()->create([
                        'type' => 'hr',
                        'start_date' => $a['sd'],
                        'end_date' => $a['ed'],
                        'back_on' => $a['bd'],
                        'comment' => $r->comment
                    ]);
                } else {
                    $item->deference()->update([
                        'type' => 'hr',
                        'start_date' => $a['sd'],
                        'end_date' => $a['ed'],
                        'back_on' => $a['bd'],
                        'comment' => $r->comment
                    ]);
                }

                $item->leave()->update([
                    'start_date' => $a['sd'],
                    'end_date' => $a['ed'],
                    'back_on' => $a['bd'],
                ]);

                $this->deduct_all($item);

                if($r->comment != null)
                {
                    $item->log()->create([
                        'type' => 'comment',
                        'user_id' => Auth::user()->id,
                        'comment' => $r->comment,
                    ]);
                }
                $item->log()->create(['comment' => 'HR approved leave request with deference']);
                $this->log(Auth::user()->id, 'HR carried out '.$r->pmode.' action on leave request '.$item->code, Request()->path());
                $item->leave->user->notify(new GeneralNotification([
                    'title' => $item->code.' leave request approved by HR with deference',
                    'url' => route('portal.leave.request.show',$item->code),
                ]));
                $item->leave->ruser->notify(new GeneralNotification([
                    'title' => $item->leave->user->fullname.' leave request has been approved, employee\'s duties have been reassigned to you from '.$item->leave->start_date.' to '.$item->leave->end_date,
                    'url' => '',
                ]));
                Session::flash('success',$item->code.' leave request approved with deference');
                return response()->json(['Leave request approved with deference'],200);
                break;
        }
    }

    private function deduct_all($lr)
    {
        $wkd = [0,6]; $count = 0;
        $days = $this->date_range($lr->leave->start_date, $lr->leave->end_date);
        foreach($days as $d)
        {
            if(!in_array(date('w',strtotime($d)),$wkd)) $count++;
        }
        $la = $lr->leave->user->leave_allocation()->where('leave_type_id',$lr->leave->leave_type_id)->first();
        $la->allowed -= $count;
        $la->update();

        // return response()->json([$count],422);
    }
}
