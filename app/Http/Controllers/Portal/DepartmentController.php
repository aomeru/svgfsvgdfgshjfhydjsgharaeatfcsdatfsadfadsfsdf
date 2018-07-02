<?php

namespace App\Http\Controllers\Portal;

use Auth;
use Crypt;
use Session;
use App\Role;
use App\User;
use App\Models\Unit;
use App\Models\Department;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Notifications\GeneralNotification;

class DepartmentController extends Controller
{
    use CommonTrait;

    public function __construct()
    {
        $this->middleware('permission:create-department', ['only' => ['storeDept']]);
        $this->middleware('permission:read-department|read-unit', ['only' => ['index']]);
        $this->middleware('permission:update-department', ['only' => ['updateDept']]);
        $this->middleware('permission:delete-department', ['only' => ['deleteDept']]);

        $this->middleware('permission:create-unit', ['only' => ['storeUnit']]);
        $this->middleware('permission:update-unit', ['only' => ['updateUnit']]);
        $this->middleware('permission:delete-unit', ['only' => ['deleteUnit']]);
    }
    public function index()
	{
		$this->log(Auth::user()->id, 'Opened the departments and units page.', Request()->path());
        return view('portal.departments.index', [
            'depts' => Department::orderby('title')->get(),
            'units' => Unit::orderby('title')->get(),
            'users' => User::select('email','firstname', 'lastname')->orderby('firstname')->get(),
			'nav' => 'settings',
			'subnav' => 'departments-and-units',
        ]);

    }

    public function storeDept(Request $r)
	{
		$rules = array(
			'dept_name' => 'required|regex:/^([a-zA-Z&\', ]+)$/|unique:departments,title',
			'head_type' => 'required|in:gm,ed',
			'head_value' => 'nullable|exists:users,email',
        );
		$validator = Validator::make($r->all(), $rules);
		if ($validator->fails()) {
			return response()->json([
				'success' => false,
				'errors' => $validator->errors()
			], 400);
		}

        $item = new Department();
        $item->title = ucwords($r->dept_name);
        if($r->head_value != null)
        {
            $user = User::where('email',$r->head_value)->first();
            if($user == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['The user selected does not exist.']]), 400);
            if($r->head_type == 'ed')
            {
                $item->ed_id = $user->id;
                $ed = Role::where('display_name','Executive Director')->first();
                if($ed != null) if(!$user->hasRole($ed->name)) $user->attachRole($ed);
            } else {
                $item->gm_id = $user->id;
                $gm = Role::where('display_name','General Manager')->first();
                if($gm != null) if(!$user->hasRole($gm->name)) $user->attachRole($gm);
            }
        }


		if($item->save()) {
            if($user != null)
            {
                $title = $r->head_type == 'ed' ? 'Executive Director' : 'General Manager';
                $user->notify(new GeneralNotification([
                    'title' => $title.' assigned to you for '.$item->title.' department',
                    'url' => ''
                ]));
            }

            $unit = new Unit();
            $unit->title = $item->title;
            $unit->department_id = $item->id;
            if($user != null) $unit->manager_id = $user->id;
            if($unit->save()){
                if($user != null){
                    $mg = Role::where('display_name','Manager')->first();
                    if($mg != null) if(!$user->hasRole($mg->name)) $user->attachRole($mg);
                    $user->unit_id = $unit->id;
                    $user->update();
                    $user->notify(new GeneralNotification([
                        'title' => 'Manager assigned to you for '.$unit->title.' unit',
                        'url' => ''
                    ]));
                }
            }
            $this->log(Auth::user()->id, 'Created '.$item->title.' department and unit with id .'.$item->id, $r->path(), 'action');
            return response()->json(array('success' => true, 'message' => 'Department & Unit created'), 200);
        }

		return response()->json(array('success' => false, 'errors' => ['errors' => ['Oops, something went wrong please try again']]), 400);
    }

    public function updateDept(Request $r)
	{
		$id = Crypt::decrypt($r->dept_id);
		$dept = Department::find($id);

		if($dept == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['This department does not exist.']]), 400);

		$rules = array(
            'dept_name' => 'required|regex:/^([a-zA-Z&\', ]+)$/|unique:departments,title,'.$dept->id,
            'head_type' => 'required|in:gm,ed',
			'head_value' => 'nullable|exists:users,email',
		);
		$validator = Validator::make($r->all(), $rules);
		if ($validator->fails()) {
			return response()->json([
				'success' => false,
				'errors' => $validator->errors()
			], 400);
		}

		$ptitle = $dept->title;
        $dept->title = ucwords($r->dept_name);
        if($r->head_value != null)
        {
            $user = User::where('email',$r->head_value)->first();
            if($user == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['The user selected does not exist.']]), 400);
            if($r->head_type == 'ed'){
                $dept->ed_id = $user->id;
                $ed = Role::where('display_name','Executive Director')->first();
                if($ed != null) if(!$user->hasRole($ed->name)) $user->attachRole($ed);
            } else {
                $dept->gm_id = $user->id;
                $gm = Role::where('display_name','General Manager')->first();
                if($gm != null) if(!$user->hasRole($gm->name)) $user->attachRole($gm);
            }
        } else {
            $dept->gm_id = null;
            $dept->ed_id = null;
        }

		if($dept->update()) {
            $this->log(Auth::user()->id, 'Updated department title from '.$ptitle.' to '.$dept->title.' with id .'.$dept->id, $r->path(),'action');
            return response()->json(array('success' => true, 'message' => $dept->title.' department updated'), 200);
        }

		return response()->json(array('success' => false, 'errors' => ['errors' => ['Oops, something went wrong please try again']]), 400);
    }

    public function deleteDept(Request $r)
	{
		$id = Crypt::decrypt($r->item_id);
		$dept = Department::find($id);

        if($dept == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['This department does not exist.']]), 400);

		if($dept->units->count() > 0) return response()->json(array('success' => false, 'errors' => ['errors' => ['Please delete '.$dept->title.' sub-units first.']]), 400);

		$did = $dept->id;
		$dtitle = $dept->title;

		if($dept->delete()){
            $this->log(Auth::user()->id, 'Deleted '.$dtitle.' department with id .'.$did, $r->path(),'action');
            return response()->json(array('success' => true, 'message' => 'Department deleted'), 200);
        }

		return response()->json(array('success' => false, 'errors' => ['errors' => ['Oops, something went wrong please try again']]), 400);
    }

    public function storeUnit(Request $r)
	{
		if($r->unit_dept_id == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['Please select a department.']]), 400);

		$id = Crypt::decrypt($r->unit_dept_id);
		$dept = Department::find($id);

		if($dept == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['The department you selected does not exist.']]), 400);

		$rules = array(
            'unit_name' => 'required|regex:/^([a-zA-Z&\', ]+)$/|unique:units,title',
            'unit_manager' => 'nullable|exists:users,email'
		);
		$validator = Validator::make($r->all(), $rules);
		if ($validator->fails()) {
			return response()->json([
				'success' => false,
				'errors' => $validator->errors()
			], 400);
		}

		$item = new Unit();
		$item->title = ucwords($r->unit_name);
        $item->department_id = $dept->id;

        if($r->unit_manager != null)
        {
            $user = User::where('email',$r->unit_manager)->first();
            if($user == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['The manager selected does not exist.']]), 400);
            $item->manager_id = $user->id;
            $m = Role::where('display_name','Manager')->first();
            if($m != null) if(!$user->hasRole($m->name)) $user->attachRole($m);
        }

        if($item->save()){
            if($user != null) {
                $user->unit_id = $item->id;
                $user->update();
                $user->notify(new GeneralNotification([
                    'title' => 'Manager assigned to you for '.$item->title.' unit',
                    'url' => ''
                ]));
            }
            $this->log(Auth::user()->id, 'Created '.$item->title.' unit with id .'.$item->id, $r->path(),'action');
            return response()->json(array('success' => true, 'message' => 'Unit created'), 200);
        }

		return response()->json(array('success' => false, 'errors' => ['errors' => ['Oops, something went wrong please try again']]), 400);
    }

    public function updateUnit(Request $r)
	{
		$unit_id = Crypt::decrypt($r->unit_id);
		$unit = Unit::find($unit_id);
		$dept = Department::where('title',$r->dept_title)->first();

		if($unit == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['This unit does not exist.']]), 400);
		if($dept == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['This department does not exist.']]), 400);

		$rules = array(
            'unit_name' => 'required|regex:/^([a-zA-Z&\', ]+)$/|unique:units,title,'.$unit->id,
            'unit_manager' => 'nullable|exists:users,email'
		);
		$validator = Validator::make($r->all(), $rules);
		if ($validator->fails()) {
			return response()->json([
				'success' => false,
				'errors' => $validator->errors()
			], 400);
		}

		$putitle = $unit->title;
		$pdtitle = $unit->department->title;

		$unit->title = ucwords($r->unit_name);
        $unit->department_id = $dept->id;

        if($r->unit_manager != null)
        {
            $user = User::where('email',$r->unit_manager)->first();
            if($user == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['The manager selected does not exist.']]), 400);
            $unit->manager_id = $user->id;
            $m = Role::where('display_name','Manager')->first();
            if($m != null) if(!$user->hasRole($m->name)) $user->attachRole($m);
            $user->unit_id = $unit->id;
            $user->update();
        } else $unit->manager_id = null;

		if($unit->update()){
            $this->log(Auth::user()->id, 'Updated unit title from '.$putitle.' to '.$unit->title.' and parent deparment from '.$putitle.' to '.$unit->department->title, $r->path(),'action');
            return response()->json(array('success' => true, 'message' => $unit->title.' unit updated'), 200);
        }

		return response()->json(array('success' => false, 'errors' => ['errors' => ['Oops, something went wrong please try again']]), 400);
    }

    public function deleteUnit(Request $r)
	{
		$id = Crypt::decrypt($r->item_id);
		$unit = Unit::find($id);

        if($unit == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['This unit does not exist.']]), 400);

		if($unit->users->count() > 0) return response()->json(array('success' => false, 'errors' => ['errors' => ['Please delete '.$unit->title.' users first.']]), 400);

		$did = $unit->id;
		$dtitle = $unit->title;

		if($unit->delete()){
            $this->log(Auth::user()->id, 'Deleted '.$dtitle.' unit with id .'.$did, $r->path(),'action');
            return response()->json(array('success' => true, 'message' => 'Unit deleted'), 200);
        }

		return response()->json(array('success' => false, 'errors' => ['errors' => ['Oops, something went wrong please try again']]), 400);
	}

}
