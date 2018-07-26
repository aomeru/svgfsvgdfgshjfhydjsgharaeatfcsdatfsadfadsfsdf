<?php

namespace App\Models\KPI;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Kpis extends Model
{
    protected $fillable = ['user_id','title','tvalue','descrip'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
