<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhotoCaption extends Model
{
    public function photo()
    {
        return $this->belongsTo('App\Photo');
    }
}
