<?php

namespace App\Http\Controllers\Portal\Leave;

use Auth;
use App\User;
use DateTime;
use DatePeriod;
use DateInterval;
use Carbon\Carbon;
use App\Models\Leave;
use App\Models\Holiday;
use App\Traits\LeaveTrait;
use App\Traits\CommonTrait;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use App\Models\LeaveAllocation;
use App\Models\LeaveRequestLog;
use App\Http\Requests\StoreLeave;
use App\Http\Requests\UpdateLeave;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Notifications\GeneralNotification;

class LeaveController extends Controller
{
    use CommonTrait;
    use LeaveTrait;

    public function __construct()
    {
        $this->middleware('permission:create-leave', ['only' => ['create','store']]);
        $this->middleware('permission:read-leave', ['only' => ['index']]);
        $this->middleware('permission:update-leave', ['only' => ['edit','update']]);
        $this->middleware('permission:delete-leave', ['only' => ['destroy']]);
        // $this->middleware('permission:update-leave-request', ['only' => ['get_cdate']]);
    }

    public function index()
    {
        // get last leave with request
        $this->log(Auth::user()->id, 'Opened the my leave page.', Request()->path());
        return view('portal.leave.leave.index', [
            'on_leave' => $this->on_leave(Auth::user()),
			'clist' => Auth::user()->leave()->whereIn('status',['pending','submitted','manager_declined'])->get(),
			'alist' => Auth::user()->leave()->whereNotIn('status',['completed','pending','manager_declined'])->get(),
			'calist' => Auth::user()->leave()->whereIn('status',['completed','called_off'])->get(),
			'las' => Auth::user()->leave_allocation()->whereHas('leave_type',function($q){ $q->orderby('title'); })->get(),
            'nav' => 'leave',
			'subnav' => 'leave',
		]);
    }

    public function create()
    {
        if($this->on_leave(Auth::user()))
        {
            Session::flash('error','You can\'t start a new leave application until you resume.');
            return redirect()->back();
        }
        if(Auth::user()->leave()->whereIn('status',['hr_approved','hr_deferred'])->count() > 0)
        {
            Session::flash('error','You already have an approved leave application.');
            return redirect()->back();
        }
        return view('portal.leave.leave.create', [
			'las' => Auth::user()->leave_allocation()->whereHas('leave_type',function($q){ $q->orderby('title'); })->get(),
            'col' => $this->get_rcolleagues(Auth::user()),
            'nav' => 'leave',
			'subnav' => 'leave',
		]);
    }

    public function store(StoreLeave $r)
    {
        if($this->on_leave(Auth::user())) return response()->json(array('success' => false, 'errors' => ['errors' => ['You can\'t create a new leave request while on leave']]), 422);

        $id = decrypt($r->ltype);
        $la = LeaveAllocation::find($id);

        $leave = Auth::user()->leave()->where('leave_type_id',$la->leave_type_id)->whereNotIn('status',['completed','called-off'])->orderby('created_at','desc')->first();
        if($leave != null) return response()->json(array('success' => false, 'errors' => ['errors' => ['You have an existing saved record for this leave type, edit it to continue if not submitted.']]), 422);

        $a = $this->leave_dates($r->start_date, $r->end_date);

        if($a['no_days'] > $la->allowed) return response()->json(['errors' => ['error' => ['You have selected more days than your allocation for this leave type.']]], 422);

        $rstaff = User::where('email',$r->rstaff)->first();
        if($this->on_leave($rstaff)) return response()->json(['errors' => ['error' => ['The relieving staff selected is on leave, please select someone else.']]], 422);

        if(Auth::user()->manager == null) return response()->json(['errors' => ['error' => ['You need to contact HR to update your manager information before you can submit this application.']]], 422);

        $item = Auth::user()->leave()->create([
            'start_date' => $a['sd'],
            'end_date' => $a['ed'],
            'back_on' => $a['bd'],
            'rstaff_id' => $rstaff->id,
            'leave_type_id' => $la->leave_type_id,
            'year' => $la->year,
            'comment' => $r->comment
        ]);

        // return response()->json(array('success' => false, 'errors' => ['errors' => [$item]]), 422);

        if($item != null) return $this->make_request($item);

        return response()->json(array('success' => false, 'errors' => ['errors' => ['Oops, something went wrong please try again']]), 422);
    }

    public function edit($id)
    {
        $id = Crypt::decrypt($id);
        $item = Leave::find($id);

        if($item == null)
        {
            Session::flash('error','This leave record does not exists');
            return redirect()->back();
        }

        if($item->user_id != Auth::user()->id)
        {
            Session::flash('error','Please ensure you own the leave record you are trying to edit');
            return redirect()->back();
        }

        if($item->leave_request != null)
        {
            $edit_allow =  ['pending','submitted','manager_declined'];
            if(!in_array($item->status,$edit_allow))
            {
                Session::flash('error','Your leave request is already being processed');
                return redirect()->back();
            }
        }

        $this->log(Auth::user()->id, 'Opened the leave item page for: '.$item->id, Request()->path());
        return view('portal.leave.leave.edit', [
            'leave' => $item,
            'col' => $this->get_rcolleagues(Auth::user()),
            'nav' => 'leave',
            'la' => Auth::user()->leave_allocation()->where('leave_type_id',$item->leave_type_id)->first(),
			'subnav' => 'leave',
		]);
        //
    }

    public function update(UpdateLeave $r)
    {
        $id = decrypt($r->id);
        $item = Leave::find($id);
        $a = $this->leave_dates($r->start_date, $r->end_date);
        $la = Auth::user()->leave_allocation()->where('leave_type_id',$item->leave_type_id)->first();

        if($a['no_days'] > $la->allowed) return response()->json(['errors' => ['error' => ['You have selected more days than your allocation for this leave type.']]], 422);

        $rstaff = User::where('email',$r->rstaff)->first();
        if($this->on_leave($rstaff)) return response()->json(['errors' => ['error' => ['The relieving staff selected is on leave, please select someone else.']]], 422);

        if(Auth::user()->manager == null) return response()->json(['errors' => ['error' => ['You need to contact HR to update your manager information before you can submit this application.']]], 422);

        $update = $item->update([
            'start_date' => $a['sd'],
            'end_date' => $a['ed'],
            'back_on' => $a['bd'],
            'rstaff_id' => $rstaff->id,
            'comment' => $r->comment
        ]);

        if($update)
        {
            $this->log(Auth::user()->id, 'Updated the leave application for: '.$item->id, Request()->path());
            return $this->make_request($item);
        }

        return response()->json(['errors' => ['error' => ['Oops, we were unable to process these changes, please try again']]], 422);

    }

    protected function make_request($l)
    {
        $lr = LeaveRequest::where('leave_id',$l->id)->first();
        if($lr == null)
        {
            $lr = new LeaveRequest;
            do {
                $code = 'LR'.str_shuffle(strtotime(now())).'-'.strtoupper(Auth::user()->username);
            } while (LeaveRequest::where('code',$code)->first() != null);
            $lr->leave_id = $l->id;
            $lr->code = $code;
            $lr->save();
        } else { $lr->manager_decision = 'pending'; $lr->status = 'submitted'; }
        $lr->manager_id = Auth::user()->manager->manager->id;
        $lr->update();

        $msg = $lr->log->count() == 0 ? 'Submitted leave request application' : 'Updated leave request and submitted application';

        $log = new LeaveRequestLog;
        $log->leave_request_id = $lr->id;
        $log->comment = $msg;
        $log->save();
        $l->update([
            'status' => 'submitted',
        ]);
        Auth::user()->manager->manager->notify(new GeneralNotification([
            'title' => Auth::user()->fullname.'\'s leave request awaiting your approval',
            'url' => route('portal.leave.request.show',$lr->code),
        ]));
        Session::flash('success','Leave application submitted successfully to '.Auth::user()->manager->manager->fullname);
        $this->log(Auth::user()->id, 'Applied for leave with code .'.$lr->code, Request()->path(), 'action');
        return response()->json(200);
    }

    public function destroy($id)
    {
        $id = Crypt::decrypt($id);
		$item = Leave::find($id);

        if($item == null)
        {
            Session::flash('error','This leave record does not exists');
            return redirect()->back();
        }

        if($item->leave_request != null)
        {
            Session::flash('error','You cannot delete a leave record that have an appplication request');
            return redirect()->back();
        }

		$did = $item->id;

		if($item->delete()){
            $this->log(Auth::user()->id, 'Deleted leave record with id .'.$did, Request()->path(),'action');
            Session::flash('success','Leave record deleted');
            return redirect()->route('portal.leave');
        }

		Session::flash('error','Could not process your request');
        return redirect()->back();
    }

    public function cancel($id)
    {
        $id = Crypt::decrypt($id);
		$item = Leave::find($id);

        if($item == null)
        {
            Session::flash('error','This leave record does not exists');
            return redirect()->back();
        }

        if(in_array($item->status, ['hr_deferred','hr_approved','hr_declined','complete','called-off']))
        {
            Session::flash('error','The leave request is already being processed, you can no longer cancel it');
            return redirect()->back();
        }

        $did = $item->id;

        $item->leave_request->deference()->delete();
        $item->leave_request->log()->delete();
        $item->leave_request()->delete();

		if($item->delete()){
            $this->log(Auth::user()->id, 'Deleted leave record with id .'.$did, Request()->path(),'action');
            Session::flash('success','Leave record deleted');
            return redirect()->route('portal.leave');
        }

		Session::flash('error','Could not process your request');
        return redirect()->back();
    }

    public function get_cdate(Request $r)
    {
        $id = Crypt::decrypt($r['ltype']);
        $la = LeaveAllocation::find($id);
        return Carbon::parse($r['start_date'])->copy()->addDays($la->allowed - 1)->format('Y-m-d');
    }

}
