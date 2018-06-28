<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laratrust\Traits\LaratrustUserTrait;

class User extends Authenticatable
{
    use LaratrustUserTrait;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getFullNameAttribute()
    {
        return ucwords($this->firstname.' '.$this->lastname);
    }

    public function getUserNameAttribute()
    {
        return explode('@',$this->email)[0];
    }

    public function manager()
	{
        return $this->hasOne(Models\UserManager::class, 'user_id');
    }

    public function users()
	{
        return $this->hasMany(Models\UserManager::class, 'manager_id');
    }

    public function unit()
    {
        return $this->belongsTo(Models\Unit::class);
    }

    public function logs()
	{
		return $this->hasMany(Models\Log::class);
    }

    public function leave_type()
	{
		return $this->hasMany(Models\LeaveType::class);
    }

    public function leave_allocation()
	{
		return $this->hasOne(Models\LeaveAllocation::class);
    }

    public function leave()
	{
		return $this->hasMany(Models\Leave::class);
    }

    public function rleave()
	{
		return $this->hasMany(Models\Leave::class, 'rstaff_id');
    }

    public function mleave()
	{
		return $this->hasMany(Models\Leave::class, 'manager_id');
    }

    public function hleave()
	{
		return $this->hasMany(Models\Leave::class, 'hr_id');
    }
}
