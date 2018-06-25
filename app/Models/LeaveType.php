<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use App\Models\LeaveAllocation;

class LeaveType extends Model
{
    protected $table = 'leave_type';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leave_allocation()
	{
		return $this->hasMany(LeaveAllocation::class);
    }

    public function leave()
	{
		return $this->hasMany(Leave::class);
    }
}
