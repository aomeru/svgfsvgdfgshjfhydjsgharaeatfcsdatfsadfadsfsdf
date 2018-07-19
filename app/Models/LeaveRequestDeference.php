<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequestDeference extends Model
{
    protected $table = 'leave_request_deference';

    protected $fillable = ['leave_request_id','start_date','end_date','back_on','type','comment'];

    public function leave_request()
    {
        return $this->belongsTo(LeaveRequest::class);
    }

}
