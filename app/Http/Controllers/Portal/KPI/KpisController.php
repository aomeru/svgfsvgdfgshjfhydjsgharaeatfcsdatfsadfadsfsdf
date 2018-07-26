<?php

namespace App\Http\Controllers\Portal\KPI;

use App\Models\KPI\Kpis;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\KPI\Setting\StoreSettingRequest;
use App\Http\Requests\KPI\Setting\UpdateSettingRequest;

class KpisController extends Controller
{
    use CommonTrait;

    public function __construct()
    {
        $this->middleware('permission:create-kpi-settings', ['only' => ['create','store']]);
        $this->middleware('permission:read-kpi-settings', ['only' => ['index']]);
        $this->middleware('permission:update-kpi-settings', ['only' => ['edit','update']]);
        $this->middleware('permission:delete-kpi-settings', ['only' => ['destroy']]);
    }

    public function index()
    {
        $this->log(Auth::user()->id, 'Opened KPI settings page.', Request()->path());
        return view('portal.KPI.settings', [
			'list' => Kpis::with('user:id,firstname,lastname')->orderby('title')->get(),
            'nav' => 'kpi',
			'subnav' => 'settings',
        ]);
    }

    protected function create_child_settings($v,$t)
    {
        $x = ['-gs-start','-gs-end','-er-start','-er-end','-fr-start','-fr-end','-sr-start','-sr-end','-gd-start','-gd-end',];
        foreach($x as $i)
        {
            $title = strtolower(str_slug($v,'-').$i);
            if(Kpis::where('title',$title)->first() == null)
            {
                Kpis::create([
                    'title' => $title,
                    'tvalue' => date('Y-m-d'),
                    'type' => 'child',
                    'descrip' => $t.' time limits',
                    'user_id' => Auth::id()
                ]);
            }
        }
    }

    public function store(StoreSettingRequest $r)
    {
        $item = Kpis::create([
            'title' => ucwords($r->stitle),
            'tvalue' => $r->svalue,
            'descrip' => $r->sdescrip,
            'user_id' => Auth::id()
        ]);

        if($r->setap) $this->create_child_settings($item->tvalue,$item->title);

        if($item != null)
        {
            $this->log(Auth::user()->id, 'Created a KPI Setting with title .'.$item->title, Request()->path(), 'action');
            return response()->json([
                'msg' => $item->title.' setting created',
                'list' => Kpis::with('user:id,firstname,lastname')->orderby('title')->get()
            ],200);
        }
        return response()->json(['errors' => ['error' => ['Oops, we were unable to process your request, please try again']]], 422);
    }

    public function update(UpdateSettingRequest $r)
    {
        $item = Kpis::where('title',$r->title)->first();
        $cc = false;
        if($item->title == 'Appraisal Period')
        {
            if($item->tvalue != $r->svalue ) $cc = true;
        }
        $update = $item->update([
            'title' => ucwords($r->stitle),
            'tvalue' => $r->svalue,
            'descrip' => $r->sdescrip,
        ]);

        if($update)
        {
            if($cc) $this->create_child_settings($item->tvalue,$item->title);
            $this->log(Auth::user()->id, 'Updated '.$item->title.' KPI Setting', Request()->path(), 'action');
            return response()->json([$item->title.' KPI setting updated'],200);
        }
        return response()->json(['errors' => ['error' => ['Oops, we were unable to process your request, please try again']]], 422);
    }

    public function destroy($t)
    {
        $item = Kpis::where('title',$t)->first();
        $dtitle = $item->title;
        if($item->delete())
        {
            $this->log(Auth::user()->id, 'Deleted KPI Setting with title .'.$dtitle, Request()->path(), 'action');
            return response()->json([$dtitle.' setting deleted'],200);
        }
        return response()->json(['errors' => ['error' => ['Oops, we were unable to process your request, please try again']]], 422);
    }
}
