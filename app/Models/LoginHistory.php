<?php

namespace ImanRjb\JwtAuth\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
