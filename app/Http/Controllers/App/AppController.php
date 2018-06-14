<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use LaraTrust;
use App\User;

class AppController extends Controller
{
    public function index()
    {
        return view('app.home');
    }

    public function process()
    {
        return false;
        $users = User::where('staff_id', 'like', 'C%')->get();
        foreach($users as $u){
            $u->employee_type = "Contract";
            $u->update();
        }
        echo $users->count();
    }
}
