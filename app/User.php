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
}
