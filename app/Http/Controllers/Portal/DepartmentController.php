<?php

namespace App\Http\Controllers\Portal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Unit;
use App\User;

class DepartmentController extends Controller
{
    public function index()
	{

		// $this->log(Auth::user()->id, 'Opened the departments and units page.', Request()->path());
        return view('portal.departments.index', [
            'depts' => Department::orderby('title')->get(),
            'units' => Unit::orderby('title')->get(),
            'users' => User::select('email','firstname', 'lastname')->orderby('firstname')->get(),
			'nav' => 'settings',
			'subnav' => 'departments-and-units',
        ]);

    }
}
