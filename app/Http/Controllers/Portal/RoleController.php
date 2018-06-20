<?php

namespace App\Http\Controllers\Portal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Role;
use App\Permission;
use App\User;
use Illuminate\Support\Facades\Validator;
use Crypt;
use Session;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('portal.roles.index', [
			'roles' => Role::orderBy('display_name')->get(),
            'nav' => 'settings',
			'subnav' => 'roles',
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
        $rules = array(
			'name' => 'required|max:100|alpha_dash|unique:roles,name',
			'display_name' => 'required|min:3|max:100|unique:roles,display_name',
			'description' => 'sometimes|max:240',
        );
		$validator = Validator::make($r->all(), $rules);
		if ($validator->fails()) {
			return response()->json([
				'success' => false,
				'errors' => $validator->errors()
			], 400);
		}

        $item = new Role();
        $item->display_name = ucwords($r->display_name);
        $item->name = $r->name;
        $item->description = $r->description;

		if($item->save()) {
            // $this->log(Auth::user()->id, 'Created '.$item->title.' department with id .'.$item->id, $r->path());
            return response()->json(array('success' => true, 'message' => 'Role created'), 200);
        }

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
        $item = Role::where('id',$id)->with(['users' => function($q){
            $q->orderBy('firstname');
        },'permissions' => function($q){
            $q->orderBy('display_name');
        }])->first();
        if($item == null){
            Session::flash('error','This Role does not exist, please confirm and try again');
            return redirect()->back();
        }
        return view('portal.roles.show', [
			'role' => $item,
			'users' => User::get(),
			'permissions' => Permission::get(),
            'nav' => 'settings',
			'subnav' => 'roles'
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
        $role = Role::where('name',$id)->first();
        if($role == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['This Role does not exist']]), 400);
        $x = 0; $new_list = []; $y = '';
        if($r->umode == 'users')
        {
            $y = $r->role_users;
            foreach($r->role_users as $e)
            {
                $user = User::where('email',$e)->first();
                if($user != null)
                {
                    if(!$user->hasRole($role->name)) $user->attachRole($role);
                    array_push($new_list,$user->id);
                }
                $x++;
            }
            foreach($role->users()->whereNotIn('id',$new_list)->get() as $rm)
            {
                $rm->detachRole($role);
            }
            $msg = 'Role Users Updated';
            $emsg = 'Unable to add some users to the given role';
        } else {
            $y = $r->role_perm;
            foreach($r->role_perm as $p)
            {
                $perm = Permission::where('name',$p)->first();
                if($perm != null)
                {
                    if(!$role->hasPermission($perm->name)) $role->attachPermission($perm);
                    array_push($new_list,$perm->id);
                }
                $x++;
            }
            foreach($role->permissions()->whereNotIn('id',$new_list)->get() as $rm)
            {
                $role->detachPermission($rm);
            }
            $msg = 'Role Permissions updated';
            $emsg = 'Unable to add some permissions to the given role';
        }

        return $x == count($y) ? response()->json(array('success' => true, 'message' => $msg), 200) : response()->json(array('success' => false, 'errors' => ['errors' => [$emsg]]), 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $r, $id)
    {
        $id = Crypt::decrypt($id);
		$item = Role::find($id);

        if($item == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['This role does not exist.']]), 400);

		// if($item->units->count() > 0) return response()->json(array('success' => false, 'errors' => ['errors' => ['Please delete '.$dept->title.' sub-units first.']]), 400);

		// $did = $item->id;
		// $dtitle = $item->title;

		if($item->delete()){
            // $this->log(Auth::user()->id, 'Deleted '.$dtitle.' department with id .'.$did, $r->path());
            return response()->json(array('success' => true, 'message' => 'Role deleted'), 200);
        }

		return response()->json(array('success' => false, 'errors' => ['errors' => ['Oops, something went wrong please try again']]), 400);
    }

    public function to_users($name)
    {
        $role = Role::where('name',$name)->first();
        if($role == null)
        {
            Session::flash('error','This role does not exist');
            return redirect()->back();
        }
        foreach(User::all() as $u)
        {
            if(!$u->hasRole($role)) $u->attachRole($role);
        }
        Session::flash('success',$role->display_name.' assigned to all users');
        return redirect()->route('roles.show',Crypt::encrypt($role->id));
    }

    public function from_users($name)
    {
        $role = Role::where('name',$name)->first();
        if($role == null)
        {
            Session::flash('error','This role does not exist');
            return redirect()->back();
        }
        foreach(User::all() as $u)
        {
            $u->detachRole($role);
        }
        Session::flash('success',$role->display_name.' removed to all users');
        return redirect()->route('roles.show',Crypt::encrypt($role->id));
    }

    public function edit_description(Request $r, $id)
    {
        $role = Role::where('name',$id)->first();
        if($role == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['This Role does not exist']]), 400);
        $rules = array(
			'description' => 'sometimes|max:240',
        );
		$validator = Validator::make($r->all(), $rules);
		if ($validator->fails()) {
			return response()->json([
				'success' => false,
				'errors' => $validator->errors()
			], 400);
		}
        $role->description = $r->description;
		if($role->update()) {
            // $this->log(Auth::user()->id, 'Created '.$item->title.' department with id .'.$item->id, $r->path());
            return response()->json(array('success' => true, 'message' => 'Role updated'), 200);
        }
		return response()->json(array('success' => false, 'errors' => ['errors' => ['Oops, something went wrong please try again']]), 400);
    }
}
