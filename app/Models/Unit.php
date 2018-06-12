<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    public function department()
	{
		return $this->belongsTo(Department::class);
    }

    public function manager()
	{
		return $this->belongsTo(\App\User::class, 'manager_id');
	}
}
