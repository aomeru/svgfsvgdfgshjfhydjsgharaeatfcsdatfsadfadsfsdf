<?php

namespace App\Http\Controllers\App;

use App\User;
use DateTime;
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
        // return false;

        $user = User::where('email','like','%speter%')->first();
        $then = new DateTime($user->date_of_hire);
        $now = new DateTime();
        // var_dump($then);
        // var_dump($now);
        dd($then->diff($now)->m + ($then->diff($now)->y*12)); // int(4)
    }
}
