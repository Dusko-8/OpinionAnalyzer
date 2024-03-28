<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Topic;
use DB;
class PostTopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $relations = [
            1 => [1, 2, 3, 4, 5, 6, 7],
            2 => [14, 15, 16, 17, 18, 19, 7],
            3 => [8, 9, 10, 11, 12, 13, 7]
            
        ];

        foreach ($relations as $postId => $topicIds) {
            foreach ($topicIds as $topicId) {
                DB::table('post_topic')->insert([
                    'post_id' => $postId,
                    'topic_id' => $topicId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
}
