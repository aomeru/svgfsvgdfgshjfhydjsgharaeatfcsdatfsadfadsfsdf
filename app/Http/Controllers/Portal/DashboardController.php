<?php

namespace App\Http\Controllers\Portal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        return view('portal.index');
    }

    public function read_notif(Request $r)
    {
        if($r->id == 'markall') return $this->read_all_notif();
        Auth::user()->unreadnotifications()->where('id',$r->id)->first()->markAsRead();
        return response()->json(['',200]);
    }

    public function read_all_notif()
    {
        foreach(Auth::user()->unreadnotifications as $n) $n->markAsRead();
        return response()->json(['',200]);
    }
}
