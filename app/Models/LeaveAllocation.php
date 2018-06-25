<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class LeaveAllocation extends Model
{
    protected $table = 'leave_allocation';

    public function leave_type()
	{
		return $this->belongsTo(LeaveType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
