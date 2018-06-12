<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use LaraTrust;

class AppController extends Controller
{
    public function index()
    {
        return view('app.home');
    }
}
