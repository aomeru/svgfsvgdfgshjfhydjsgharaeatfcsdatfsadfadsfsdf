<?php

namespace App\Http\Controllers\Portal\KPI;

use App\Models\KPI\Kpi;
use App\Models\KPI\Kpig;
use App\Traits\KpiTrait;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\KPI\Goals\StoreGoalsRequest;

class KpigController extends Controller
{
    use CommonTrait;
    use KpiTrait;

    public function __construct()
    {
        $this->middleware('permission:create-kpi-goals', ['only' => ['create','store']]);
        $this->middleware('permission:read-kpi-goals', ['only' => ['index']]);
        $this->middleware('permission:update-kpi-goals', ['only' => ['edit','update']]);
        $this->middleware('permission:delete-kpi-goals', ['only' => ['destroy']]);
    }
    protected function get_kpis()
    {
        return Kpi::where('user_id',Auth::id())->with(['goals' => function($q){$q->where('parent_id',null)->orderBy('goal','asc')->with('goals');}])->orderby('created_at','desc')->get();
    }

    public function index()
    {
        $ap = $this->current_apy();
        // $ckpi = Kpi::where('user_id',Auth::id())->where('appraisal_period',$ap)->value('id');
        $this->log(Auth::user()->id, 'Opened KPI goals page.', Request()->path());
        return view('portal.KPI.goals', [
            'list' => $this->get_kpis(),
            'cm' => $this->can_make_kpi(),
            'ap' => $ap,
            // 'ckpi' => $ckpi == null ? null : encrypt($ckpi),
            'nav' => 'kpi',
			'subnav' => 'goals',
        ]);
    }

    protected function create_kpi()
    {
        $item = Kpi::create([
            'user_id' => Auth::id(),
            'appraisal_period' => $this->current_apy()
        ]);

        return $item;
    }

    public function store(StoreGoalsRequest $r)
    {
        $kpi = $this->current_kpi();
        if($kpi == null ) $kpi = $this->create_kpi();

        $exists = Kpig::where('kpi_id',$kpi->id)->where('goal',$r->goal)->first();
        if($exists != null) return response()->json(['errors' => ['error' => ['You have an existing similar goal.']]], 422);

        $item = Kpig::create([
            'kpi_id' => $kpi->id,
            'goal' => $r->goal,
            'weight' => $r->weight,
        ]);

        if($r->is_sub_goal)
        {
            $item->update([
                'parent_id' => $r->parent_id
            ]);
        }

        if($item != null)
        {
            $this->log(Auth::id(), 'Created a KPI goal with ID: '.$item->id, Request()->path(), 'action');
            return response()->json([
                'msg' => 'KPI Goal created',
                'list' => $this->get_kpis(),
            ],200);
        }
        return response()->json(['errors' => ['error' => ['Oops, we were unable to process your request, please try again']]], 422);
    }
}
