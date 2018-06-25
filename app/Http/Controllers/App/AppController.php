<?php

namespace App\Http\Controllers\App;

use App\User;
use LaraTrust;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use App\Models\LeaveAllocation;
use App\Http\Controllers\Controller;

class AppController extends Controller
{
    public function index()
    {
        return view('app.home');
    }

    public function test()
    {
        return false;
        $users = User::inRandomOrder()->get();
        $users = User::orderByRaw('RAND()')->take(20)->get();
        foreach($users as $user)
        {
            foreach(LeaveType::all() as $item)
            {
                // dd($item->title);
                $all = new LeaveAllocation();
                $all->leave_type_id = $item->id;
                $all->allowed = $item->allowed;
                $all->year = 2018;
                $user->leave_allocation()->save($all);
                // $all->user_id = $user->id;
                // $all->save();
            }
        }
    }
}
