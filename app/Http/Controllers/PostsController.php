<?php

namespace App\Http\Controllers;

use App\Photo;

class PostsController extends Controller
{
    public function index()
    {
        $photos = Photo::all();
        foreach ($photos as $photo) {
            $photo['likes'] = $photo->likes;
            $photo['comments'] = $photo->comments;
            $photo['tags'] = $photo->tags;
            $photo['users_in_photo'] = $photo->usersInPhoto;
            $photo['photo_caption'] = $photo->photoCaption;
            $photo['users'] = $photo->user;
        }

        return response()->json([
            'photos'    => $photos,
        ]);
    }
}
