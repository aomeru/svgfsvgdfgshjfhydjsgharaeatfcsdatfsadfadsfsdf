<?php

namespace App\Models\KPI;

use App\Models\KPI\Kpig;
use Illuminate\Database\Eloquent\Model;

class Kpi extends Model
{
    protected $table = 'kpi';
    // protected $guared = ['user_id'];
    protected $fillable = ['user_id','appraisal_period','approval','status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function goals()
    {
        return $this->hasMany(Kpig::class);
    }
}
