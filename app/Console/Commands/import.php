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
            $this->insertComments($post_data);
            $all_post_here[] =  $post_data;
        }

        return 0;
    }
}
