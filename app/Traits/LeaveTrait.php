<?php
namespace App\Traits;

use Auth;
use App\User;
use DateTime;
use DateInterval;
use App\Models\Holiday;

trait LeaveTrait
{
    public function on_leave($user)
    {
        $x = $user->leave()->has('leave_request')->where('status','hr_approved')->orderby('created_at','desc')->first();
        return $x == null ? false : true;
    }

    public function get_colleagues($user)
    {
        return User::where('id','<>',$user->id)->where('status','active')->where('unit_id',$user->unit_id)->orderby('firstname')->get();
    }

    public function get_rcolleagues($user)
    {
        $x = [];
        $users = User::where('id','<>',$user->id)->where('status','active')->where('unit_id',$user->unit_id)->orderby('firstname')->get();
        foreach($users as $v)
        {
            if(!$this->on_leave($v)) array_push($x,$v);
        }
        return $x;
    }

    public function check_start_date($date)
    {

    }

    public function date_range($first, $last, $step = '+1 day', $output_format = 'Y-m-d' )
    {
        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);
        while( $current <= $last )
        {
            $dates[] = date($output_format, $current);
            $current = strtotime($step, $current);
        }
        return $dates;
    }

    public function get_holiday_array($s,$e='')
    {
        $v = [];
        foreach(Holiday::whereBetween('start_date',[$s,$e])->orderby('start_date')->get() as $h)
        {
            $hsd = new DateTime($h->start_date); $hsd = $hsd->format('Y-m-d');
            if($h->end_date != null){
                $hed = new DateTime($h->end_date);
                $hed = $hed->format('Y-m-d');
                $hdays = $this->date_range($hsd,$hed);
            } else {
                $hdays = [$hsd];
            }
            foreach($hdays as $hd)
            {
                if(!in_array($hd,$v)) array_push($v,$hd);
            }
        }
        return $v;
    }

    protected function leave_dates($start_date, $end_date)
    {
        $sd = new DateTime($start_date); $sdx = $sd->format('Y-m-d');
        $ed = new DateTime($end_date); $edx = $ed->format('Y-m-d');
        $period = $this->date_range($sdx,$edx);
        $wkd = [0,6]; $d = 0; $add = 0; $sd_add = false;
        $hols = $this->get_holiday_array($sd,$ed);

        foreach($period as $p)
        {
            if(!in_array(date('w',strtotime($p)),$wkd))
            {
                if(in_array($p,$hols))
                {
                    if($period[0] == $p) $sd_add = true;
                    $add++;
                }
            }
            $d++;
        }

        if($add > 0)
        {
            $ed->add(new DateInterval('P'.$add.'D'))->format('Y-m-d');
            if($sd_add) $sd->add(new DateInterval('P'.$add.'D'))->format('Y-m-d');
        }

        $bd = new DateTime($ed->format('Y-m-d'));
        do {
            $bd->add(new DateInterval('P1D'))->format('Y-m-d');
        } while(in_array(date('w',strtotime($bd->format('Y-m-d'))),$wkd));

        return [
            'sd' => $sd,
            'ed' => $ed,
            'bd' => $bd,
            'no_days' => $d,
        ];
    }

    
}
