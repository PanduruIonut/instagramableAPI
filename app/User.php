<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    public function photo()
    {
        return $this->belongsTo('App\Photo');
    }
}
