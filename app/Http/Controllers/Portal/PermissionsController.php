<?php

namespace App\Http\Controllers\Portal;

use App\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Role;
use App\User;
use Illuminate\Support\Facades\Validator;
use Crypt;
use Session;
use Auth;
use Laratrust;
use App\Traits\CommonTrait;


class PermissionsController extends Controller
{
    use CommonTrait;

    public function __construct()
    {
        $this->middleware('permission:create-permission', ['only' => ['store']]);
        $this->middleware('permission:read-permission');
        $this->middleware('permission:update-permission', ['only' => ['edit_description']]);
        $this->middleware('permission:assign-remove-role|permission:assign-remove-permission', ['only' => ['update']]);
        $this->middleware('permission:delete-permission', ['only' => ['delete']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->log(Auth::user()->id, 'Opened the permissions page.', Request()->path());
        return view('portal.permissions.index', [
			'list' => Permission::orderBy('display_name')->get(),
            'nav' => 'settings',
			'subnav' => 'permissions',
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
        if($r->permmode == 'basic')
        {
            $rules = array(
                'name' => 'required|max:100|alpha_dash|unique:permissions,name',
                'display_name' => 'required|min:3|max:100|unique:permissions,display_name',
                'moduleb' => 'sometimes|max:20',
                'description' => 'sometimes|max:240',
            );
            $validator = Validator::make($r->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 400);
            }
            $item = new Permission();
            $item->display_name = ucwords($r->display_name);
            $item->name = $r->name;
            $item->module = ucwords($r->moduleb);
            $item->description = $r->description;

            if($item->save()) {
                $this->log(Auth::user()->id, 'Created '.$item->display_name.' permission with id .'.$item->id, $r->path(),'action');
                $role = Role::where('display_name','System Administrator')->first();
                $role->attachPermission($item);
                return response()->json(array('success' => true, 'message' => 'Permission created'), 200);
            }
        } else {
            $rules = array(
                'module_crud' => 'required|min:3|max:100',
                'crud' => 'required',
            );
            $validator = Validator::make($r->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 400);
            }
            $x = 0;
            $ca = ['create','read','update','delete'];
            foreach($r->crud as $a)
            {
                if(in_array($a,$ca))
                {
                    $t = $a.'-'.strtolower($r->module_crud);
                    if(Permission::where('id',$t)->first() == null)
                    {
                        $perm = new Permission;
                        $perm->display_name = ucwords($a).' '.ucwords($r->module_crud);
                        $perm->module = ucwords($r->module_crud);
                        $perm->name = $t;
                        $perm->description = 'Has ability to '.$a.' '.$r->module_crud;
                        if($perm->save())
                        {
                            $this->log(Auth::user()->id, 'Created '.$perm->display_name.' permission with id .'.$perm->id, $r->path(),'action');
                            $role = Role::where('display_name','System Administrator')->first();
                            $role->attachPermission($perm);
                        }
                    }
                }
            }
            return response()->json(array('success' => true, 'message' => 'Permission created'), 200);
        }

		return response()->json(array('success' => false, 'errors' => ['errors' => ['Oops, something went wrong please try again']]), 400);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id = Crypt::decrypt($id);
        $item = Permission::where('id',$id)->with(['users' => function($q){
            $q->orderBy('firstname');
        },'roles' => function($q){
            $q->orderBy('display_name');
        }])->first();
        if($item == null){
            Session::flash('error','This Permission does not exist, please confirm and try again');
            return redirect()->back();
        }
        $this->log(Auth::user()->id, 'Opened the '.$item->display_name.' permission page', Request()->path());
        return view('portal.permissions.show', [
			'perm' => $item,
			'users' => User::get(),
			'roles' => Role::get(),
            'nav' => 'settings',
			'subnav' => 'permissions'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission $permission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $r, $id)
    {
        $perm = Permission::where('name',$id)->first();
        if($perm == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['This Permission does not exist']]), 400);
        $x = 0; $new_list = []; $y = '';
        if($r->umode == 'users')
        {
            return response()->json(array('success' => false, 'errors' => ['errors' => ['Please use the role module to assign permissions to users through their roles']]), 400);

            $y = $r->perm_users;
            if($y != null)
            {
                foreach($y as $e)
                {
                    $user = User::where('email',$e)->first();
                    if($user != null)
                    {
                        if(!$user->hasPermission($perm->name)) $user->attachPermission($perm);
                        array_push($new_list,$user->id);
                    }
                    $x++;
                }
            }
            foreach($perm->users()->whereNotIn('id',$new_list)->get() as $rm)
            {
                $rm->detachPermission($perm);
            }
            $msg = 'Permission Users updated';
            $emsg = 'Unable to assign some permissions to the selected users';
        } else {
            $y = $r->perm_roles;
            if($y != null)
            {
                foreach($y as $p)
                {
                    $role = Role::where('name',$p)->first();
                    if($role != null)
                    {
                        if(!$role->hasPermission($perm->name))
                        { 
                            $role->attachPermission($perm);
                            $this->log(Auth::user()->id, 'Attached the "'.$perm->display_name.'" permission to '.$role->display_name.' role', $r->path(),'action');
                        }
                        array_push($new_list,$role->id);
                    }
                    $x++;
                }
            }
            foreach($perm->roles()->whereNotIn('id',$new_list)->get() as $rm)
            {
                $rm->detachPermission($perm);
                $this->log(Auth::user()->id, 'Removed the "'.$perm->display_name.'" permission from "'.$rm->display_name.'" role', $r->path(),'action');
            }
            $msg = 'Permission Roles updated';
            $emsg = 'Unable to assigned permission to some selected roles';
        }

        if(!is_array($y)) return response()->json(array('success' => true, 'message' => $msg), 200);
        return $x == count($y) ? response()->json(array('success' => true, 'message' => $msg), 200) : response()->json(array('success' => false, 'errors' => ['errors' => [$emsg]]), 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $r, $id)
    {
        $id = Crypt::decrypt($id);
		$item = Permission::find($id);

        if($item == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['This permission does not exist.']]), 400);

		$did = $item->id;
		$dtitle = $item->display_name;

		if($item->delete()){
            $this->log(Auth::user()->id, 'Deleted '.$dtitle.' permission with id .'.$did, $r->path());
            return response()->json(array('success' => true, 'message' => 'Permission deleted'), 200);
        }

		return response()->json(array('success' => false, 'errors' => ['errors' => ['Oops, something went wrong please try again']]), 400);
    }

    public function to_users($name)
    {
        Session::flash('warning','Please use the role module to assign permissions to users through roles');
        return redirect()->route('roles.index');

        $perm = Permission::where('name',$name)->first();
        if($perm == null)
        {
            Session::flash('error','This permission does not exist');
            return redirect()->back();
        }
        foreach(User::get() as $u)
        {
            if(!$u->hasPermission($perm->name)) $u->attachPermission($perm->name);
        }
        Session::flash('success',$perm->display_name.' assigned to all users');
        return redirect()->route('permissions.show',Crypt::encrypt($perm->id));
    }

    public function from_users($name)
    {
        $perm = Permission::where('name',$name)->first();
        if($perm == null)
        {
            Session::flash('error','This permission does not exist');
            return redirect()->back();
        }
        foreach(User::get() as $u)
        {
            $u->detachPermission($perm->name);
        }
        Session::flash('success',$perm->display_name.' removed from all users');
        $this->log(Auth::user()->id, 'Removed '.$perm->display_name.' permission from all users', $r->path(),'action');
        return redirect()->route('permissions.show',Crypt::encrypt($perm->id));
    }

    public function from_roles($name)
    {
        $perm = Permission::where('name',$name)->first();
        if($perm == null)
        {
            Session::flash('error','This permission does not exist');
            return redirect()->back();
        }
        foreach(Role::get() as $r)
        {
            $r->detachPermission($perm->name);
        }
        Session::flash('success',$perm->display_name.' removed from all roles');
        $this->log(Auth::user()->id, 'Removed '.$perm->display_name.' permission from all roles', $r->path(),'action');
        return redirect()->route('permissions.show',Crypt::encrypt($perm->id));
    }

    public function to_roles($name)
    {
        $perm = Permission::where('name',$name)->first();
        if($perm == null)
        {
            Session::flash('error','This permission does not exist');
            return redirect()->back();
        }
        foreach(Role::get() as $r)
        {
            if(!$r->hasPermission($perm->name)) $r->attachPermission($perm);
        }
        Session::flash('success',$perm->display_name.' assigned to all roles');
        $this->log(Auth::user()->id, 'Assigned '.$perm->display_name.' permission to all roles', $r->path(),'action');
        return redirect()->route('permissions.show',Crypt::encrypt($perm->id));
    }

    public function edit_description(Request $r, $id)
    {
        $perm = Permission::where('name',$id)->first();
        if($perm == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['This Permission does not exist']]), 400);
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
        $pdes = $perm->description;
        $perm->description = $r->description;
		if($perm->update()) {
            $this->log(Auth::user()->id, 'Updated '.$perm->display_name.' description from '.$pdes.' to '.$perm->description, $r->path(),'action');
            return response()->json(array('success' => true, 'message' => 'Permission updated'), 200);
        }
		return response()->json(array('success' => false, 'errors' => ['errors' => ['Oops, something went wrong please try again']]), 400);
    }
}
