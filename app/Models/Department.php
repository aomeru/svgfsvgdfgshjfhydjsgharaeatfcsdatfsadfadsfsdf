<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    public function units()
	{
		return $this->hasMany(Unit::class);
    }

    public function ed()
	{
		return $this->belongsTo(\App\User::class, 'ed_id');
    }

    public function gm()
	{
		return $this->belongsTo(\App\User::class, 'gm_id');
	}
}
