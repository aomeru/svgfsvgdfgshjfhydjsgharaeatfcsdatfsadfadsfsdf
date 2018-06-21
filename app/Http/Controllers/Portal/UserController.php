<?php

namespace App\Http\Controllers\Portal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Models\UserManager;
use App\Models\Unit;
use Crypt;
use Session;
use Illuminate\Support\Facades\Validator;
use App\Traits\CommonTrait;
use Auth;
use Laratrust;

class UserController extends Controller
{
    use CommonTrait;

    public function __construct()
    {
        $this->middleware('permission:create-user', ['only' => ['store']]);
        $this->middleware('permission:read-user');
        $this->middleware('permission:update-user', ['only' => ['update']]);
        // $this->middleware('permission:delete-user', ['only' => ['delete']]);
    }

    public function index()
    {
		$this->log(Auth::user()->id, 'Opened the users page.', Request()->path());
        return view('portal.users.index', [
			'list' => User::whereIn('status',['active','inactive'])->orderBy('firstname')->get(),
            'units' => Unit::orderBy('title')->get(),
            'nav' => 'users',
			'subnav' => 'all-users',
		]);
    }

    public function update(Request $r)
	{
		$id = Crypt::decrypt($r->user_id);
		$user = User::find($id);

		if($user == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['This user does not exist.']]), 400);

		$rules = array(
			'staff_id' => 'nullable|regex:/^([a-zA-Z0-9-]*)$/|unique:users,staff_id,'.$user->id,
			'unit_id' => 'nullable|exists:units,title',
			'manager' => 'nullable|exists:users,email',
			'doh' => 'nullable|date',
			'status' => 'required|in:active,inactive,deactivated',
			'emp_type' => 'required|in:Graduate Trainee,Full Time,Part Time,Contract',
		);
		$validator = Validator::make($r->all(), $rules);
		if ($validator->fails()) {
			return response()->json([
				'success' => false,
				'errors' => $validator->errors()
			], 400);
        }

        $psid = $user->staff_id;
        if($user->manager != null) $pm = $user->manager->manager->email;
        if($user->unit != null) $punit = $user->unit->title;
		$pdoh = $user->date_of_hire;
		$pemt = $user->employee_type;
		$ps = $user->status;

        if(Laratrust::can('update-override'))
        {
            $user->staff_id = $r->staff_id;
            $user->date_of_hire = $r->doh;
        }

		$user->unit_id = Unit::where('title',$r->unit_id)->value('id');
		$user->status = $r->status;
        $user->employee_type = $r->emp_type;

		if($user->update())
		{
            $manager = UserManager::where('user_id',$user->id)->first();
            if($manager != null)
            {
                $manager->manager_id = User::where('email',$r->manager)->value('id');
                $manager->update();
            } else {
                $manager = new UserManager;
                $manager->user_id = $user->id;
                $manager->manager_id = User::where('email',$r->manager)->value('id');
                $manager->save();
            }


            $umsg = 'Updated user account:';
            if($psid != $user->staff_id) $umsg .= ' changed staffID from '.$psid.' to '.$user->staff_id;
            // if(isset($pm)){ $nm = $user->manager->manager->email; if($pm != $nm) $umsg .= '; changed manager from '.$pm.' to '.$nm; }
            // if(isset($punit)){ $nunit = $user->unit->title; if($punit != $nunit) $umsg .= '; changed unit from '.$punit.' to '.$nunit; }
            if($pdoh != $user->date_of_hire) $umsg .= '; changed date of hire from '.$pdoh.' to '.$user->date_of_hire;
            if($pemt != $user->employee_type) $umsg .= '; changed employee type from '.$pemt.' to '.$user->employee_type;
            if($ps != $user->status) $umsg .= '; changed status from '.$ps.' to '.$user->status;
			$this->log(Auth::user()->id,$umsg,$r->path(),'action');
			return response()->json(array('success' => true, 'message' => $user->firstname.' account updated'), 200);
		}

		return response()->json(array('success' => false, 'errors' => ['errors' => ['Oops, something went wrong please try again']]), 400);
	}
}
