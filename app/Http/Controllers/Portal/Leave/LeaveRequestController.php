<?php

namespace App\Http\Controllers\Portal\Leave;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LeaveRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:manager|general-manager|executive-director|leave-manager');
        $this->middleware('permission:update-leave-request', ['only' => ['show','update']]);
    }
}
