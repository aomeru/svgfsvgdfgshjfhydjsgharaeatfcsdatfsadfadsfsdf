<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserManager extends Model
{
    public function user()
	{
		return $this->belongsTo(\App\User::class, 'user_id', 'id');
    }

    public function manager()
	{
		return $this->belongsTo(\App\User::class, 'manager_id', 'id');
	}
}
