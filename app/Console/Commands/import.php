<?php

namespace App\Console\Commands;

use App\Comment;
use App\Filters;
use App\LikedPhotos;
use App\Photo;
use App\PhotoCaption;
use App\Tags;
use App\User;
use App\UsersInPhoto;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class import extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for running json import';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function insertComments(array $req)
    {
        if (!empty($req['comments']->data)) {
            foreach ($req['comments']->data as $comment) {
                $newComment = new Comment();
                $newComment->created_time = $req['created_time'];
                $newComment->user_id = $comment->from->id;
                $newComment->photo_id = $req['id'];
                $newComment->text = $comment->text;
                $newComment->save();
            }
        }
    }

    public function insertTags(array $req)
    {
        if (!empty($req['tags'])) {
            foreach ($req['tags'] as $tag) {
                $newTag = new Tags();
                $newTag->photo_id = $req['id'];
                $newTag->name = $tag;
                $newTag->save();
            }
        }
    }

    public function insertUsers(array $req)
    {
        if (!empty($req['user'])) {
            $newUser = new User();
            $newUser->id = $req['user']->id;
            $newUser->username = $req['user']->username;
            $newUser->website = $req['user']->website;
            $newUser->bio = $req['user']->bio;
            $newUser->profile_picture = $req['user']->profile_picture;
            $newUser->full_name = $req['user']->full_name;
            $newUser->save();
        }

        if (!empty($req['comments']->data)) {
            foreach ($req['comments']->data as $comment) {
                $existingUser = User::find($comment->from->id);
                if (!$existingUser) {
                    $user = $comment->from;
                    $newUser = new User();
                    $newUser->id = $user->id;
                    $newUser->username = $user->username;
                    $newUser->profile_picture = $user->profile_picture;
                    $newUser->full_name = $user->full_name;
                    $newUser->save();
                }
            }
        }

        if (!empty($req['likes']->data)) {
            foreach ($req['likes']->data as $user) {
                $existingUser = User::find($user->id);
                if (!$existingUser) {
                    $newUser = new User();
                    $newUser->id = $user->id;
                    $newUser->username = $user->username;
                    $newUser->profile_picture = $user->profile_picture;
                    $newUser->full_name = $user->full_name;
                    $newUser->save();
                }
            }
        }
    }

    public function insertCaptions(array $req)
    {
        if (!empty($req['caption'])) {
            $newCaption = new PhotoCaption();
            $newCaption->id = $req['caption']->id;
            $newCaption->text = $req['caption']->text;
            $newCaption->photo_id = $req['id'];
            $newCaption->user_id = $req['caption']->from->id;
            $newCaption->save();
        }
    }

    public function insertFilters(array $req)
    {
        if (!empty($req['filter'])) {
            $newFilter = new Filters();
            $newFilter->name = $req['filter'];
            $newFilter->save();
        }
    }

    public function insertUsersInPhoto(array $req)
    {
        if (!empty($req['users_in_photo'])) {
            foreach ($req['users_in_photo'] as $userInPhoto) {
                $newUserInPhoto = new UsersInPhoto();
                $newUserInPhoto->x_coord = $userInPhoto->position->x;
                $newUserInPhoto->y_coord = $userInPhoto->position->y;
                $newUserInPhoto->user_id = $userInPhoto->user->id;
                $newUserInPhoto->photo_id = $req['id'];
                $newUserInPhoto->save();
            }
        }
    }

    public function insertLikedPhotos(array $req)
    {
        if (!empty($req['likes'])) {
            foreach ($req['likes']->data as $likes) {
                $newLike = new LikedPhotos();
                $newLike->user_id = $likes->id;
                $newLike->photo_id = $req['id'];
                $newLike->save();
            }
        }
    }

    public function insertPhotos(array $req)
    {
        if (!empty($req)) {
            $newPhoto = new Photo();
            $newPhoto->id = $req['id'];
            $newPhoto->created_time = $req['created_time'];
            $newPhoto->lat = $req['location']->latitude;
            $newPhoto->long = $req['location']->longitude;
            $newPhoto->filter = Filters::where('name', $req['filter'])->value('id');
            $newPhoto->link = $req['link'];
            $newPhoto->save();
        }
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $content = file_get_contents('https://raw.githubusercontent.com/robynitp/networkedmedia/master/week5/00-json/instagram.json');
        $obj = json_decode($content);
        $all_post_here  = [];
        foreach ($obj->data as $post) {
            $post_data['likes'] = $post->likes;
            $post_data['user'] = $post->user;
            $post_data['comments'] = $post->comments;
            $post_data['filter'] = $post->filter;
            $this->insertUsers($post_data);
            $this->insertFilters($post_data);
        }
        foreach ($obj->data as $post) {
            $post_data['tags'] = $post->tags;
            $post_data['location'] = $post->location;
            $post_data['comments'] = $post->comments;
            $post_data['filter'] = $post->filter;
            $post_data['created_time'] = $post->created_time;
            $post_data['link'] = $post->link;
            $post_data['likes'] = $post->likes;
            $post_data['images'] = $post->images;
            $post_data['users_in_photo'] = $post->users_in_photo;
            $post_data['caption'] = $post->caption;
            $post_data['type'] = $post->type;
            $post_data['id'] = $post->id;
            $post_data['user'] = $post->user;

            $this->insertPhotos($post_data);
            $this->insertCaptions($post_data);
            $this->insertComments($post_data);
            $this->insertTags($post_data);
            $this->insertUsersInPhoto($post_data);
            $this->insertLikedPhotos($post_data);

            $all_post_here[] =  $post_data;
        }

        return 0;
    }
}
