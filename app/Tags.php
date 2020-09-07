<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tags extends Model
{
    public function photos()
    {
        return $this->belongsToMany('App\Photo');
    }
}
