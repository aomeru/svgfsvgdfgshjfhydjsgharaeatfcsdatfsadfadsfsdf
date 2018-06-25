<?php

namespace App\Http\Controllers\Portal\Leave;

use Auth;
use Crypt;
use Session;
use Laratrust;
use App\Models\LeaveType;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LeaveTypeController extends Controller
{
    use CommonTrait;

    public function __construct()
    {
        $this->middleware('permission:create-leave-type', ['only' => ['store']]);
        $this->middleware('permission:read-leave-type');
        $this->middleware('permission:update-leave-type', ['only' => ['update']]);
        $this->middleware('permission:delete-leave-type', ['only' => ['delete']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->log(Auth::user()->id, 'Opened the leave type page.', Request()->path());
        return view('portal.leave.type', [
			'list' => LeaveType::orderBy('title')->get(),
            'nav' => 'leave',
			'subnav' => 'leave-type',
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
            'year' => 'required|numeric',
            'title' => 'required|unique:leave_type,title',
            'type' => 'sometimes|in:calculated,static',
            'allowed' => 'sometimes|numeric',
            'callowed' => 'sometimes',
        );
        $validator = Validator::make($r->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }
        $item = new LeaveType();
        $item->user_id = Auth::user()->id;
        $item->title = strtoupper($r->title);
        $item->type = $r->type;
        $item->allowed = $r->type == 'static' ? $r->allowed : $r->callowed;

        if($item->save()) {
            $this->log(Auth::user()->id, 'Created '.$item->title.' leave type with id .'.$item->id, $r->path(),'action');
            return response()->json(array('success' => true, 'message' => 'Leave Type created'), 200);
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
        //
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
        $item = LeaveType::find($id);
        if($item == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['This leave type does not exist']]), 400);

        $rules = array(
            'year' => 'required|numeric',
            'title' => 'required|unique:leave_type,title,'.$id,
            'type' => 'sometimes|in:calculated,static',
            'allowed' => 'sometimes|numeric',
            'callowed' => 'sometimes',
        );
        $validator = Validator::make($r->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $item->user_id = Auth::user()->id;
        $item->title = strtoupper($r->title);
        $item->type = $r->type;
        $item->allowed = $r->type == 'static' ? $r->allowed : $r->callowed;

        if($item->update()) {
            $this->log(Auth::user()->id, 'Updated '.$item->title.' leave type with id .'.$item->id, $r->path(),'action');
            return response()->json(array('success' => true, 'message' => 'Leave Type updated'), 200);
        }

        return response()->json(array('success' => false, 'errors' => ['errors' => ['Oops, something went wrong please try again']]), 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = Crypt::decrypt($id);
		$item = LeaveType::find($id);

        if($item == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['This leave type item does not exist.']]), 400);

		$did = $item->id;
		$dtitle = $item->title;

		if($item->delete()){
            $this->log(Auth::user()->id, 'Deleted '.$dtitle.' leave type with id .'.$did, Request()->path());
            return response()->json(array('success' => true, 'message' => 'Leave Type deleted'), 200);
        }

		return response()->json(array('success' => false, 'errors' => ['errors' => ['Oops, something went wrong please try again']]), 400);
    }
}
