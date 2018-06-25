<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $table = 'leave';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ruser()
    {
        return $this->belongsTo(User::class, 'rstaff_id');
    }

    public function type()
	{
		return $this->belongsTo(LeaveType::class);
    }

    public function request()
	{
		return $this->hasOne(LeaveRequest::class);
    }
}