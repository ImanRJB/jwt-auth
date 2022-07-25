<?php

namespace ImanRjb\JwtAuth\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $table = 'oauth_access_tokens';
    protected $casts = ['id' => 'string'];

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
