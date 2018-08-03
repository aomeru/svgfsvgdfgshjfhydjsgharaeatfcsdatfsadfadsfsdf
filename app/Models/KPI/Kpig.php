<?php

namespace App\Models\KPI;

use App\User;
use App\Models\KPI\Kpi;
use App\Models\KPI\Kpig;
use Illuminate\Database\Eloquent\Model;

class Kpig extends Model
{
    protected $fillable = ['kpi_id','goal','parent_id','weight'];

    public function kpi()
    {
        return $this->belongsTo(Kpi::class);
    }

    public function goal()
    {
        return $this->belongsTo($this, 'parent_id');
    }

    public function goals()
    {
        return $this->hasMany($this, 'parent_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
