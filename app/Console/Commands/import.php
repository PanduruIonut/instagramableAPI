<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use DateTime;
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
            $created_datetime = $req['created_time'];
            $photo_id = $req['id'];

            foreach ($req['comments']->data as $comment) {
                $user_id = $comment->from->id;
                $text = $comment->text;
                $data = array('created_time' => $created_datetime, 'user_id' => $user_id, 'photo_id' => $photo_id, 'text' => $text);
                DB::table('comments')->insert($data);
            }
        }
    }

    public function insertTags(array $req)
    {
        if (!empty($req['tags'])) {
            $photo_id = $req['id'];

            foreach ($req['tags'] as $tag) {
                $name = $tag;
                $data = array('photo_id' => $photo_id, 'name' => $name);
                DB::table('tags')->insert($data);
            }
        }
    }

    public function insertUsers(array $req)
    {
        if (!empty($req['user'])) {
            $id = $req['user']->id;
            $username = $req['user']->username;
            $website = $req['user']->website;
            $bio = $req['user']->bio;
            $profilePicture = $req['user']->profile_picture;
            $fullName = $req['user']->full_name;
            $data = array('id' => $id, 'username' => $username, 'website' => $website, 'bio' => $bio, 'profile_picture' => $profilePicture, 'full_name' => $fullName);
            DB::table('users')->updateOrInsert($data);
        }

        if (!empty($req['comments']->data)) {
            foreach ($req['comments']->data as $comment) {
                $user = $comment->from;
                $id = $user->id;
                $username = $user->username;
                $profilePicture = $user->profile_picture;
                $fullName = $user->full_name;
                $website = '';
                $bio = '';
                $data = array('id' => $id, 'username' => $username, 'website' => $website, 'bio' => $bio, 'profile_picture' => $profilePicture, 'full_name' => $fullName);
                DB::table('users')->updateOrInsert($data);
            }
        }

        if (!empty($req['likes']->data)) {
            foreach ($req['likes']->data as $user) {
                $id = $user->id;
                $username = $user->username;
                $profilePicture = $user->profile_picture;
                $fullName = $user->full_name;
                $website = '';
                $bio = '';
                $data = array('id' => $id, 'username' => $username, 'website' => $website, 'bio' => $bio, 'profile_picture' => $profilePicture, 'full_name' => $fullName);
                DB::table('users')->updateOrInsert($data);
            }
        }
    }

    public function insertCaptions(array $req)
    {
        if (!empty($req['caption'])) {
            $id = $req['caption']->id;
            $text = $req['caption']->text;
            $photo_id = $req['id'];
            $user_id = $req['caption']->from->id;
            $data = array('id' => $id, 'text' => $text, 'photo_id' => $photo_id, 'user_id' => $user_id);
            DB::table('photo_captions')->insert($data);
        }
    }

    public function insertFilters(array $req)
    {
        if (!empty($req['filter'])) {
            $name = $req['filter'];
            $data = array('name' => $name);
            DB::table('filters')->updateOrInsert($data);
        }
    }

    public function insertUsersInPhoto(array $req)
    {
        if (!empty($req['users_in_photo'])) {
            $photo_id = $req['id'];

            foreach ($req['users_in_photo'] as $userInPhoto) {
                $x_coord = $userInPhoto->position->x;
                $y_coord = $userInPhoto->position->y;
                $user_id = $userInPhoto->user->id;
                $data = array('x_coord' => $x_coord, 'y_coord' => $y_coord, 'user_id' => $user_id, 'photo_id' => $photo_id);
                DB::table('users_in_photos')->insert($data);
            }
        }
    }

    public function insertLikedPhotos(array $req)
    {
        if (!empty($req['likes'])) {
            $photo_id = $req['id'];

            foreach ($req['likes']->data as $likes) {
                $user_id = $likes->id;
                $data = array('user_id' => $user_id, 'photo_id' => $photo_id);
                DB::table('liked_photos')->insert($data);
            }
        }
    }

    public function insertPhotos(array $req)
    {
        if (!empty($req)) {
            $id = $req['id'];
            $created_time = $req['created_time'];
            $latitude = $req['location']->latitude;
            $longitude = $req['location']->longitude;
            $longitude = $req['location']->longitude;
            $link = $req['link'];
            $filter = DB::table('filters')->where('name', $req['filter'])->value('id');
            $data = array('id' => $id, 'created_time' => $created_time, 'lat' => $latitude, 'long' => $longitude, 'filter' => $filter, 'link' => $link);
            DB::table('photos')->insert($data);
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
