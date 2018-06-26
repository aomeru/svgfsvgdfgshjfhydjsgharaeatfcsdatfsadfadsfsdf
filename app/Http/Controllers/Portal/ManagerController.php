<?php

namespace App\Http\Controllers\Portal;

use App\Role;
use App\User;
use App\Models\Unit;
use App\Models\Department;
use App\Models\UserManager;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ManagerController extends Controller
{
    use CommonTrait;

    public function __construct()
    {
        $this->middleware('permission:create-manager', ['only' => ['store']]);
        $this->middleware('permission:read-manager');
        $this->middleware('permission:update-manager', ['only' => ['update']]);
        $this->middleware('permission:delete-manager', ['only' => ['delete']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->log(Auth::user()->id, 'Opened the user manager page.', Request()->path());
        $manager = Role::where('display_name','Manager')->first();
        return view('portal.users.manager.index', [
			'list' => $manager->users()->orderBy('firstname')->get(),
			'musers' => User::doesntHave('users')->get(),
			'users' => User::all(),
			'depts' => Department::all(),
			'units' => Unit::all(),
            'nav' => 'users',
			'subnav' => 'managers',
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
            'manager' => 'required|exists:users,email',
            'unit' => 'required|exists:units,title',
            'unit_manager' => 'in:true,false',
            'unit_users' => 'required|in:or-unit,or-users',
            'user_unit' => 'in:true,false',
            'staffs' => 'sometimes|max:240',
        );
        $validator = Validator::make($r->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }
        $manager = User::where('email',$r->manager)->first();
        $unit = Unit::where('title',$r->unit)->first();
        $manager->unit_id = $unit->id;
        if($manager->update()) $this->log(Auth::user()->id, 'Updated staff unit for '.$manager->firstname.' '.$manager->lastname.' to '.$unit->title, $r->path());
        if($r->unit_users == 'or-unit')
        {
            foreach($unit->users()->where('id','<>',$manager->id)->get() as $user)
            {
                $item = $user->manager == null ? new UserManager() : $user->manager;
                $item->manager_id = $manager->id;
                $item->user_id = $user->id;
                if($item->save()){
                    $this->log(Auth::user()->id, 'Updated '.$user->firstname.' '.$user->lastname.' manager to '.$manager->firstname.' '.$manager->lastname, $r->path());
                    if($r->user_unit)
                    {
                        $user->unit_id = $unit->id;
                        if($user->update()) $this->log(Auth::user()->id, 'Updated staff unit for '.$user->firstname.' '.$user->lastname.' to '.$unit->title, $r->path());
                    }
                }
            }
        } else {
            foreach($r->staffs as $e)
            {
                $user = User::where('email',$e)->first();
                $item = $user->manager == null ? new UserManager() : $user->manager;
                $item->manager_id = $manager->id;
                $item->user_id = $user->id;
                if($item->save())
                {
                    $this->log(Auth::user()->id, 'Updated '.$user->firstname.' '.$user->lastname.' manager to '.$manager->firstname.' '.$manager->lastname, $r->path());
                    if($r->user_unit)
                    {
                        $user->unit_id = $unit->id;
                        if($user->update()) $this->log(Auth::user()->id, 'Updated staff unit for '.$user->firstname.' '.$user->lastname.' to '.$unit->title, $r->path());
                    }
                }
            }
        }

        if($r->unit_manager)
        {
            $unit->manager_id = $manager->id;
            if($unit->update()) $this->log(Auth::user()->id, 'Updated '.$unit->title.' unit manager to '.$manager->firstname.' '.$manager->lastname, $r->path());
        }

        $role = Role::where('display_name','Manager')->first();
        if(!$manager->hasRole($role->name))
        {
            $manager->attachRole($role);
            $this->log(Auth::user()->id, 'Assigned '.$role->display_name.' role to '.$manager->firstname.' '.$manager->lastname, $r->path());
        }
        return response()->json(array('success' => true, 'message' => 'User Manager created'), 200);

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
        // $item = User::where('id',$id)->with('users', function($q){
        //     $q->orderBy('firstname');
        // })->first();
        $item = User::find($id);
        if($item == null){
            Session::flash('error','This manager record does not exist, please confirm and try again');
            return redirect()->back();
        }
        $this->log(Auth::user()->id, 'Opened the '.$item->firstname.' '.$item->lastname.' manager page', Request()->path());
        return view('portal.users.manager.show', [
			'manager' => $item,
			'users' => User::get(),
            'nav' => 'users',
			'subnav' => 'managers'
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
        //
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
        $id = Crypt::decrypt($id);
        $manager = User::find($id);
        if($manager == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['This manager record does not exist']]), 400);
        $x = 0;
        foreach($r->users as $e)
        {
            $user = User::where('email',$e)->first();
            if($user != null)
            {
                $item = $user->manager == null ? new UserManager : $user->manager;
                $item->user_id = $user->id;
                $item->manager_id = $manager->id;
                if($item->save())
                {
                    $this->log(Auth::user()->id, 'Updated '.$user->firstname.' '.$user->lastname.' manager to '.$manager->firstname.' '.$manager->lastname, $r->path());
                    if($r->user_unit)
                    {
                        $unit = Unit::where('title',$manager->unit->title)->first();
                        $user->unit_id = $unit->id;
                        if($user->update()) $this->log(Auth::user()->id, 'Updated '.$user->firstname.' '.$user->lastname.' unit to '.$unit->title, $r->path());
                    }
                }
                $x++;
            }
        }

        return $x == count($r->users) ? response()->json(array('success' => true, 'message' => 'Manager subordinate updated'), 200) : response()->json(array('success' => false, 'errors' => ['errors' => ['Unable to update all manager subordinate information']]), 400);
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
		$item = User::find($id);

        if($item == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['This manager record does not exist.']]), 400);

		$dtitle = $item->firstname.' '.$item->lastname;

		if($item->users()->delete()){
            if(!$item->hasRole('Manager')) $item->detachRole('Manager');
            if($item->unit != null)
            {
                $unit = Unit::where('title',$item->unit->title)->first();
                $unit->manager_id = null;
                $unit->update();
            }
            $this->log(Auth::user()->id, 'Deleted '.$dtitle.' manager records', $r->path());
            return response()->json(array('success' => true, 'message' => 'Manager record deleted'), 200);
        }

		return response()->json(array('success' => false, 'errors' => ['errors' => ['Oops, something went wrong please try again']]), 400);
    }
}
