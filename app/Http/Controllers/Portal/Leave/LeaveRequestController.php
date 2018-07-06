<?php

namespace App\Http\Controllers\Portal\Leave;

use App\Traits\LeaveTrait;
use App\Traits\CommonTrait;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LeaveRequestController extends Controller
{
    use CommonTrait;
    use LeaveTrait;

    public function __construct()
    {
        $this->middleware('role:manager|general-manager|executive-director|leave-manager');
        $this->middleware('permission:update-leave-request', ['only' => ['show','update']]);
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
        $this->log(Auth::user()->id, 'Opened the leave request '.$item->code.' page.', Request()->path());
        // check manager
        // check permission
        // dd($item->log);
        return view('portal.leave.request.show', [
			'item' => $item,
            'nav' => 'leave',
			'subnav' => 'leave-requests',
		]);
    }
}
