<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function filters()
    {
        return $this->hasMany('App\Filters');
    }

    public function likes()
    {
        return $this->hasMany('App\LikedPhotos');
    }

    public function photoCaption()
    {
        return $this->hasOne('App\PhotoCaption');
    }

    public function tags()
    {
        return $this->hasMany('App\Tags');
    }

    public function usersInPhoto()
    {
        return $this->hasMany('App\UsersInPhoto');
    }
}
