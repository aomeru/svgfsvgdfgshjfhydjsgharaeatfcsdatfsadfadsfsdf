<?php

namespace App\Http\Controllers\Portal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Models\Unit;
class UserController extends Controller
{
    public function index()
    {
		// $this->log(Auth::user()->id, 'Opened the users page.', Request()->path());

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

        return response()->json(array('success' => false, 'errors' => ['errors' => ['This user does not exist.']]), 400);

		$pfirstname = $user->firstname;
		$plastname = $user->lastname;
		$pemail = $user->email;
		$pgender = $user->gender;
		$prole = $user->username;
		$punit = $user->unit == null ? '' : $user->unit->title;
		$psid = $user->staff_id;
		$pstatus = $user->status;

		$user->firstname = $r->firstname;
		$user->lastname = $r->lastname;
		$user->email = $r->email;
		$e = explode('@',$r->email);
        $user->username = strtolower($e[0]);
		$user->gender = $r->gender;
		$user->role_id = Role::where('title',$r->role_id)->value('id');
		$user->unit_id = Unit::where('title',$r->unit_id)->value('id');
		$user->staff_id = $r->staff_id;
		$user->status = $r->status;

		if($user->update())
		{
			$this->log(Auth::user()->id,
				'Updated user account; firstname from "'.$pfirstname.'" to "'.$user->firstname.'",
				lastname from "'.$plastname.'" to "'.$user->lastname.'",
				email from "'.$pemail.'" to "'.$user->email.'",
				gender from "'.$pgender.'" to "'.$user->gender.'",
				account status from "'.$pstatus.'" to "'.$user->status.'",
				staff ID from "'.$psid.'" to "'.$user->staff_id.'",
				role from "'.$prole.'" to "'.$user->username.'",
				unit from "'.$punit.'" to unit with ID "'.$user->unit_id.'",
				on user id .'.$user->id,
				$r->path());
			return response()->json(array('success' => true, 'message' => $user->firstname.' account updated'), 200);
		}

		return response()->json(array('success' => false, 'errors' => ['errors' => ['Oops, something went wrong please try again']]), 400);
	}
}
