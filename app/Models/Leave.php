<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    // protected $table = 'leaves';

    protected $fillable = ['user_id', 'leave_type_id', 'rstaff_id', 'start_date', 'end_date', 'back_on', 'call_off', 'year', 'status', 'comment'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ruser()
    {
        return $this->belongsTo(User::class, 'rstaff_id');
    }

    public function leave_type()
	{
		return $this->belongsTo(LeaveType::class);
    }

    public function leave_request()
	{
		return $this->hasOne(LeaveRequest::class);
    }

    public function cstatus()
	{
        $status = '';
        $x = $this->leave_request(LeaveRequest::class);
        $manager = ['submitted'];
        $hr = ['submitted'];
    }
}
