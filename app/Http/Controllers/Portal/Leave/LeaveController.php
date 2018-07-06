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
use Illuminate\Http\Request;
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
        $this->middleware('permission:create-leave', ['only' => ['store']]);
        $this->middleware('permission:read-leave');
        $this->middleware('permission:update-leave', ['only' => ['edit','update']]);
        $this->middleware('permission:delete-leave', ['only' => ['destroy']]);
    }

    public function index()
    {
        // get last leave with request

        $this->log(Auth::user()->id, 'Opened the my leave page.', Request()->path());
        return view('portal.leave.index', [
            'on_leave' => $this->on_leave(Auth::user()),
			'clist' => Auth::user()->leave()->whereIn('status',['pending','submitted','manager_declined'])->get(),
			'alist' => Auth::user()->leave()->whereNotIn('status',['completed','pending','manager_declined'])->get(),
			'calist' => Auth::user()->leave()->where('status','completed')->get(),
			'las' => Auth::user()->leave_allocation()->whereHas('leave_type',function($q){ $q->orderby('title'); })->get(),
            'nav' => 'leave',
			'subnav' => 'leave',
		]);
    }

    public function store(StoreLeave $r)
    {
        $la = Auth::user()->leave_allocation()->whereHas('leave_type',function($q) use ($r){
            $q->where('title',$r->ltype);
        })->first();
        if($this->on_leave(Auth::user())) return response()->json(array('success' => false, 'errors' => ['errors' => ['You can\'t create a new leave request while on leave']]), 422);

        $leave = Auth::user()->leave()->where('leave_type_id',$la->leave_type_id)->has('leave_request',0)->first();
        if($leave != null) return response()->json(array('success' => false, 'errors' => ['errors' => ['You have an existing saved record for this leave type, edit it to continue']]), 422);

        // check the leave status

        $item = Auth::user()->leave()->create([
            'start_date' => $r->start_date,
            'leave_type_id' => $la->leave_type_id,
            'year' => $la->year
        ]);
        if($item)
        {
            $this->log(Auth::user()->id, 'Created a leave record with id .'.$item->id, $r->path(), 'action');
            return response()->json(array('success' => true, 'msg' => Crypt::encrypt($item->id)), 200);
        }

        return response()->json(array('success' => false, 'errors' => ['errors' => ['Oops, something went wrong please try again']]), 422);
    }

    public function show($id)
    {
        dd('show');
        //
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
        return view('portal.leave.create.apply', [
            'leave' => $item,
            'col' => $this->get_rcolleagues(Auth::user()),
            'nav' => 'leave',
            'la' => Auth::user()->leave_allocation()->where('leave_type_id',$item->leave_type_id)->first(),
			'subnav' => 'leave',
		]);
        //
    }

    public function update(UpdateLeave $r, $id)
    {
        $id = Crypt::decrypt($id);
        $item = Leave::find($id);
        $sd = new DateTime($r->start_date); $sdx = $sd->format('Y-m-d');
        $ed = new DateTime($r->end_date); $edx = $ed->format('Y-m-d');
        $period = $this->date_range($sdx,$edx);
        $wkd = [0,6]; $d = 0; $add = 0; $sd_add = false;
        $hols = $this->get_holiday_array($sd,$ed);

        // return response()->json(['errors' => ['error' => [$hols]]], 422);

        foreach($period as $p)
        {
            if(!in_array(date('w',strtotime($p)),$wkd))
            {
                if(in_array($p,$hols))
                {
                    if($period[0] == $p) $sd_add = true;
                    $add++;
                }
            }
            $d++;
        }

        $la = Auth::user()->leave_allocation()->where('leave_type_id',$item->leave_type_id)->first();
        if($d > $la->allowed) return response()->json(['errors' => ['error' => ['You have selected more days than your allocation for this leave type.']]], 422);

        if($add > 0)
        {
            $ed->add(new DateInterval('P'.$add.'D'))->format('Y-m-d');
            if($sd_add) $sd->add(new DateInterval('P'.$add.'D'))->format('Y-m-d');
        }

        $rstaff = User::where('email',$r->rstaff)->first();
        if($this->on_leave($rstaff)) return response()->json(['errors' => ['error' => ['The relieving staff selected is on leave, please select someone else.']]], 422);

        $bd = new DateTime($ed->format('Y-m-d'));
        // return response()->json(['errors' => ['error' => [$bd,$ed]]], 422);
        do {
            $bd->add(new DateInterval('P1D'))->format('Y-m-d');
        } while(in_array(date('w',strtotime($bd->format('Y-m-d'))),$wkd));

        // return response()->json(['errors' => ['error' => [$bd,$ed]]], 422);

        $update = $item->update([
            'start_date' => $sd,
            'end_date' => $ed,
            'back_on' => $bd,
            'rstaff_id' => $rstaff->id
        ]);
        if($update)
        {
            if($r->action == 'submit'){
                $item->update(['comment' => $r->comment]);
                return $this->make_request($item);
            }
            $this->log(Auth::user()->id, 'Updated the leave application for: '.$item->id, Request()->path());
            return response()->json(array('success' => true, 'message' => 'Leave updated'), 200);
        }

        return response()->json(['errors' => ['error' => ['Oops, we were unable to process these changes, please try again']]], 422);

    }

    protected function make_request($l)
    {
        if($l->user->manager == null) return response()->json(['errors' => ['error' => ['You need to contact HR to update your manager information before you can submit this application.']]], 422);

        if($l->leave_request == null)
        {
            $l->leave_request()->create([
                'code' => 'LR'.str_shuffle(strtotime(now())).'-'.strtoupper($l->user->username),
            ]);
        }
        $l->leave_request()->update([
            'manager_id' => $l->user->manager->manager->id
        ]);
        if($l->leave_request->log->count() > 0)
        {
            $l->leave_request->log()->create([
                'comment' => 'Leave request updated',
            ]);
        } else {
            $l->leave_request->log()->create([
                'comment' => 'Leave request submitted',
            ]);
        }
        $l->update([
            'status' => 'submitted',
        ]);
        $l->user->manager->manager->notify(new GeneralNotification([
            'title' => $l->user->fullname.'\'s leave request awaiting your approval',
            'url' => route('portal.leave.approval',$l->leave_request->code),
        ]));
        Session::flash('success','Leave application submitted successfully to '.$l->user->manager->manager->fullname);
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

    public function get_cdate(Request $r)
    {
        return Carbon::parse($r['params']['start_date'])->copy()->addDays($r['params']['allowed'] - 1)->format('Y-m-d');
    }
}