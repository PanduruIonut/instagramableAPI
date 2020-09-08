<?php

namespace App\Http\Controllers;

use App\Photo;

class PostsController extends Controller
{
    public function index()
    {

        return Photo::with('likes', 'comments', 'photoCaption', 'tags', 'usersInPhoto')->get();
    }
}
