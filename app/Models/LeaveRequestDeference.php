<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequestDeference extends Model
{
    protected $table = 'leave_request_deference';

    public function leave_request()
    {
        return $this->belongsTo(LeaveRequest::class);
    }

}
