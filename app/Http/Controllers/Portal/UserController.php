<?php

namespace App\Http\Controllers\Portal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Models\Unit;
class UserController extends Controller
{
    public function index()
    {
		// $this->log(Auth::user()->id, 'Opened the users page.', Request()->path());

        return view('portal.users.index', [
			'nav' => 'users',
			'list' => User::orderBy('firstname')->get(),
            'units' => Unit::orderBy('title')->get(),
            'nav' => 'users',
			'subnav' => 'all-users',
		]);
    }
}
