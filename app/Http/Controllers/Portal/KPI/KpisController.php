<?php

namespace App\Http\Controllers\Portal\KPI;

use App\Models\KPI\Kpis;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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
			'list' => Kpis::all(),
            'nav' => 'kpi',
			'subnav' => 'settings',
        ]);
    }
}
