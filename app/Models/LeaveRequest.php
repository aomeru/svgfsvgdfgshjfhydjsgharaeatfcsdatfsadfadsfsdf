<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $table = 'leave_request';

    protected $fillable = ['code','manager_id','manager_decision','manager_decision_date','hr_id','hr_decision','hr_decision_date','status'];

    protected $dates = [
        'created_at',
        'updated_at',
        'manager_decision_date',
        'hr_decision_date'
    ];

    public function leave()
	{
		return $this->belongsTo(Leave::class);
    }

    public function manager()
	{
		return $this->belongsTo(User::class, 'manager_id');
    }

    public function hr()
	{
		return $this->belongsTo(User::class, 'hr_id');
    }

    public function log()
	{
		return $this->hasMany(LeaveRequestLog::class, 'leave_request_id');
    }

    public function deference()
	{
		return $this->hasOne(LeaveRequestDeference::class, 'leave_request_id');
    }
}
