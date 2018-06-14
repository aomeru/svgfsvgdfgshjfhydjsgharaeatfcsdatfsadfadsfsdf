<?php

namespace App\Http\Controllers\Portal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Unit;
use App\User;
use Session;
use Crypt;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    public function index()
	{

		// $this->log(Auth::user()->id, 'Opened the departments and units page.', Request()->path());
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
            $user_id = User::where('email',$r->head_value)->value('id');
            if($user_id == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['The user selected does not exist.']]), 400);
            if($r->head_type == 'ed') $item->ed_id = $user_id; else $item->gm_id = $user_id;
        }


		if($item->save()) {
            // $this->log(Auth::user()->id, 'Created '.$item->title.' department with id .'.$item->id, $r->path());
            $unit = new Unit();
            $unit->title = $item->title;
            $unit->department_id = $item->id;
            $unit->save();
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
            $user_id = User::where('email',$r->head_value)->value('id');
            if($user_id == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['The user selected does not exist.']]), 400);
            if($r->head_type == 'ed') $dept->ed_id = $user_id; else $dept->gm_id = $user_id;
        } else {
            $dept->gm_id = null;
            $dept->ed_id = null;
        }

		if($dept->update()) {
            // $this->log(Auth::user()->id, 'Updated department title from '.$ptitle.' to '.$dept->title.' with id .'.$dept->id, $r->path());
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
            // $this->log(Auth::user()->id, 'Deleted '.$dtitle.' department with id .'.$did, $r->path());
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
            $user_id = User::where('email',$r->unit_manager)->value('id');
            if($user_id == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['The manager selected does not exist.']]), 400);
            $item->manager_id = $user_id;
        }

        if($item->save()){
            // $this->log(Auth::user()->id, 'Created '.$item->title.' unit with id .'.$item->id, $r->path());
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

		// $putitle = $unit->title;
		// $pdtitle = $unit->department->title;

		$unit->title = ucwords($r->unit_name);
        $unit->department_id = $dept->id;

        if($r->unit_manager != null)
        {
            $user_id = User::where('email',$r->unit_manager)->value('id');
            if($user_id == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['The manager selected does not exist.']]), 400);
            $unit->manager_id = $user_id;
        } else $unit->manager_id = null;

		if($unit->update()){
            // $this->log(Auth::user()->id, 'Updated unit title from '.$putitle.' to '.$unit->title.' and parent deparment from '.$putitle.' to '.$unit->department->title, $r->path());
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

		// $did = $unit->id;
		// $dtitle = $unit->title;

		if($unit->delete()){
            // $this->log(Auth::user()->id, 'Deleted '.$dtitle.' unit with id .'.$did, $r->path());
            return response()->json(array('success' => true, 'message' => 'Unit deleted'), 200);
        }

		return response()->json(array('success' => false, 'errors' => ['errors' => ['Oops, something went wrong please try again']]), 400);
	}



}
