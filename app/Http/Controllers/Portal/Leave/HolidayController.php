<?php

namespace App\Http\Controllers\Portal\Leave;

use App\User;
use App\Models\Holiday;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\HolidayRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class HolidayController extends Controller
{
    use CommonTrait;

    public function __construct()
    {
        $this->middleware('permission:create-holiday', ['only' => ['store']]);
        $this->middleware('permission:read-holiday');
        $this->middleware('permission:update-holiday', ['only' => ['update']]);
        $this->middleware('permission:delete-holiday', ['only' => ['delete']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->log(Auth::user()->id, 'Opened the holiday page.', Request()->path());
        return view('portal.leave.holiday', [
			'list' => Holiday::orderBy('start_date')->get(),
            'nav' => 'leave',
			'subnav' => 'holiday',
		]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(HolidayRequest $r)
    {
        $item = Auth::user()->holiday()->create([
            'title' => ucwords($r->title),
            'start_date' => $r->start_date,
            'end_date' => $r->end_date == null ? null : $r->end_date,
        ]);

        $user = User::where('email',config('app.it_email'))->first();

        if($item) {
            $this->log(Auth::user()->id, 'Created '.$item->title.' holiday with id .'.$item->id, $r->path(),'action');
            return response()->json(array('success' => true, 'message' => 'Holiday created'), 200);
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
        $item = Holiday::find($id);
        if($item == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['This holiday record does not exist']]), 400);

        $rules = array(
            'title' => 'required|unique:holidays,title,'.$id,
            'start_date' => 'required|date||unique:holidays,start_date,'.$id,
            'end_date' => 'sometimes|nullable|date|after:start_date|unique:holidays,end_date,'.$id,
        );
        $messages = [
            'start_date.unique' => 'The start date already exists in a different holiday  record',
            'end_date.after' => 'The holiday end date must be after the start date',
            'end_date.unique' => 'The end date already exists in a different holiday  record',
        ];
        $validator = Validator::make($r->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $item->user_id = Auth::user()->id;
        $item->title = ucwords($r->title);
        $item->start_date = $r->start_date;
        $item->end_date = $r->end_date == null ? null : $r->end_date;

        if($item->update()) {
            $this->log(Auth::user()->id, 'Updated '.$item->title.' holiday with id .'.$item->id, $r->path(),'action');
            return response()->json(array('success' => true, 'message' => 'Holiday updated'), 200);
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
		$item = Holiday::find($id);

        if($item == null) return response()->json(array('success' => false, 'errors' => ['errors' => ['This holiday record does not exist.']]), 400);

		$did = $item->id;
		$dtitle = $item->title;

		if($item->delete()){
            $this->log(Auth::user()->id, 'Deleted '.$dtitle.' holiday record with id .'.$did, Request()->path());
            return response()->json(array('success' => true, 'message' => 'Holiday deleted'), 200);
        }

		return response()->json(array('success' => false, 'errors' => ['errors' => ['Oops, something went wrong please try again']]), 400);
    }
}
