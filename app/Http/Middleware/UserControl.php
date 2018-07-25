<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\LeaveTrait;
use Illuminate\Support\Facades\Auth;

class UserControl
{
    use LeaveTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // dd('working');
        // if($this->on_leave(Auth::user()))
        {
            $leaves = Auth::user()->leave()->whereIn('status',['hr_approved','hr_deferred'])->where('end_date','<',date('Y-m-d'))->orderby('created_at','desc')->get();
            // dd($leaves);
            foreach($leaves as $y)
            {
                $y->update([
                    'status' => 'completed'
                ]);
                $y->leave_request()->update([
                    'status' => 'completed'
                ]);
            }
        }
        return $next($request);
    }
}
