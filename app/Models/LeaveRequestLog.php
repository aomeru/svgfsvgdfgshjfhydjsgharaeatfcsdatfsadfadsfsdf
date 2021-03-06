<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class LeaveRequestLog extends Model
{
    protected $table = 'leave_request_log';

    protected $fillable = ['leave_request_id','user_id','type','comment'];

    public function leave_request()
    {
        return $this->belongsTo(LeaveRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
