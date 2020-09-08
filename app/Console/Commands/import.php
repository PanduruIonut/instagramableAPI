<?php

namespace App\Console\Commands;

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
            $username = $req['user']->username;
            $website = $req['user']->website;
            $bio = $req['user']->bio;
            $profilePicture = $req['user']->profile_picture;
            $fullName = $req['user']->full_name;
            $data = array('username' => $username, 'website' => $website, 'bio' => $bio, 'profile_picture' => $profilePicture, 'full_name' => $fullName);
            DB::table('users')->insert($data);
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
            $photo_id = $req['id'];
            $name = $req['filter'];
            $data = array('name' => $name, 'photo_id' => $photo_id);
            DB::table('filters')->insert($data);
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
            $this->insertCaptions($post_data);
            $this->insertComments($post_data);
            $this->insertUsers($post_data);
            $this->insertTags($post_data);
            $this->insertFilters($post_data);
            $this->insertUsersInPhoto($post_data);
            $all_post_here[] =  $post_data;
        }

        return 0;
    }
}
