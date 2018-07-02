<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = ['title', 'start_date', 'end_date', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
