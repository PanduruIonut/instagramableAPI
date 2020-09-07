<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LikedPhotos extends Model
{
    public function photos()
    {
        return $this->belongsTo('App\Photos');
    }
}
