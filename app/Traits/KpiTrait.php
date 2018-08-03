<?php
namespace App\Traits;

use Auth;
use App\User;
use DateTime;
use DateInterval;
use App\Models\KPI\Kpi;
use App\Models\KPI\Kpis;

trait KpiTrait
{
    public function can_make_kpi()
    {
        $l = Kpis::where('title','Period Limit')->first();
        if($l->tvalue == 'true')
        {
            $s = Kpis::where('title','Employee Limit')->first();
            if($s == null) return true;

            $allowed = array_map('trim',explode(',',$s->tvalue));
            if(!in_array(strtolower(Auth::user()->employee_type),$allowed)) return false;
        }

        return true;
    }

    public function current_apy()
    {
        return Kpis::where('title','Appraisal Period')->value('tvalue');
    }

    public function current_kpi()
    {
        return Kpi::where('user_id',Auth::id())->where('appraisal_period',$this->current_apy())->first();
    }
}
