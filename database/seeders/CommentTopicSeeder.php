<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\Topic;
use DB;

class CommentTopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Example of comment and topic associations
        $associations_EU = [
            ['comment_id' => 1, 'topic_id' => 1],
            ['comment_id' => 1, 'topic_id' => 4],
            ['comment_id' => 1, 'topic_id' => 6],
            // New comment;
            ['comment_id' => 2, 'topic_id' => 1],
            ['comment_id' => 2, 'topic_id' => 6],
            // New comment;
            ['comment_id' => 3, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 4, 'topic_id' => 1],
            ['comment_id' => 4, 'topic_id' => 6],
            // New comment;
            ['comment_id' => 5, 'topic_id' => 2],
            ['comment_id' => 5, 'topic_id' => 6],
            // New comment;
            ['comment_id' => 6, 'topic_id' => 3],
            ['comment_id' => 6, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 7, 'topic_id' => 6],
            // New comment;
            ['comment_id' => 8, 'topic_id' => 6],
            // New comment;
            ['comment_id' => 9, 'topic_id' => 7],
            // New comment;
            ['comment_id' => 10, 'topic_id' => 1],
            ['comment_id' => 10, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 11, 'topic_id' => 4],
            ['comment_id' => 11, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 12, 'topic_id' => 3],
            ['comment_id' => 12, 'topic_id' => 6],
            // New comment;
            ['comment_id' => 13, 'topic_id' => 3],
            // New comment;
            ['comment_id' => 14, 'topic_id' => 2],
            ['comment_id' => 14, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 15, 'topic_id' => 1],
            ['comment_id' => 15, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 16, 'topic_id' => 1],
            ['comment_id' => 16, 'topic_id' => 6],
            // New comment;
            ['comment_id' => 17, 'topic_id' => 3],
            ['comment_id' => 17, 'topic_id' => 6],
            // New comment;
            ['comment_id' => 18, 'topic_id' => 6],
            ['comment_id' => 18, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 19, 'topic_id' => 6],
            ['comment_id' => 19, 'topic_id' => 2],
            // New comment;
            ['comment_id' => 20, 'topic_id' => 5],
            ['comment_id' => 20, 'topic_id' => 1],
            // New comment;
            ['comment_id' => 21, 'topic_id' => 3],
            ['comment_id' => 21, 'topic_id' => 6],
            ['comment_id' => 21, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 22, 'topic_id' => 6],
            // New comment;
            ['comment_id' => 23, 'topic_id' => 1],
            ['comment_id' => 23, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 24, 'topic_id' => 2],
            ['comment_id' => 24, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 25, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 26, 'topic_id' => 7],
            // New comment;
            ['comment_id' => 27, 'topic_id' => 2],
            ['comment_id' => 27, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 28, 'topic_id' => 1],
            ['comment_id' => 28, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 29, 'topic_id' => 2],
            ['comment_id' => 29, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 30, 'topic_id' => 4],
            ['comment_id' => 30, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 31, 'topic_id' => 4],
            ['comment_id' => 31, 'topic_id' => 5],
            ['comment_id' => 31, 'topic_id' => 1],
            // New comment;
            ['comment_id' => 32, 'topic_id' => 1],
            ['comment_id' => 32, 'topic_id' => 2],
            ['comment_id' => 32, 'topic_id' => 6],
            // New comment;
            ['comment_id' => 33, 'topic_id' => 6],
            ['comment_id' => 33, 'topic_id' => 7],
            // New comment;
            ['comment_id' => 34, 'topic_id' => 6],
            // New comment;
            ['comment_id' => 35, 'topic_id' => 4],
            ['comment_id' => 35, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 36, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 37, 'topic_id' => 1],
            ['comment_id' => 37, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 38, 'topic_id' => 2],
            // New comment;
            ['comment_id' => 39, 'topic_id' => 6],
            // New comment;
            ['comment_id' => 40, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 42, 'topic_id' => 2],
            ['comment_id' => 42, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 43, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 44, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 45, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 46, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 47, 'topic_id' => 5],
            ['comment_id' => 47, 'topic_id' => 2],
            // New comment;
            ['comment_id' => 48, 'topic_id' => 6],
            // New comment;
            ['comment_id' => 49, 'topic_id' => 6],
            ['comment_id' => 49, 'topic_id' => 4],
            // New comment;
            ['comment_id' => 50, 'topic_id' => 1],
            ['comment_id' => 50, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 51, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 52, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 53, 'topic_id' => 2],
            // New comment;
            ['comment_id' => 54, 'topic_id' => 2],
            ['comment_id' => 54, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 55, 'topic_id' => 2],
            // New comment;
            ['comment_id' => 56, 'topic_id' => 1],
            ['comment_id' => 56, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 57, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 61, 'topic_id' => 1],
            ['comment_id' => 61, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 62, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 63, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 64, 'topic_id' => 1],
            ['comment_id' => 64, 'topic_id' => 2],
            // New comment;
            ['comment_id' => 65, 'topic_id' => 4],
            ['comment_id' => 65, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 66, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 67, 'topic_id' => 1],
            ['comment_id' => 67, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 68, 'topic_id' => 1],
            ['comment_id' => 68, 'topic_id' => 5],
            ['comment_id' => 68, 'topic_id' => 4],
            // New comment;
            ['comment_id' => 69, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 70, 'topic_id' => 1],
            ['comment_id' => 70, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 71, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 72, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 73, 'topic_id' => 1],
            // New comment;
            ['comment_id' => 74, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 75, 'topic_id' => 5],
            ['comment_id' => 75, 'topic_id' => 1],
            // New comment;
            ['comment_id' => 76, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 77, 'topic_id' => 1],
            ['comment_id' => 77, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 78, 'topic_id' => 1],
            ['comment_id' => 78, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 79, 'topic_id' => 5],
            ['comment_id' => 79, 'topic_id' => 2],
            // New comment;
            ['comment_id' => 80, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 81, 'topic_id' => 1],
            ['comment_id' => 81, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 82, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 83, 'topic_id' => 5],
            ['comment_id' => 83, 'topic_id' => 2],
            ['comment_id' => 83, 'topic_id' => 3],
            // New comment;
            ['comment_id' => 84, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 85, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 86, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 87, 'topic_id' => 1],
            ['comment_id' => 87, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 88, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 89, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 90, 'topic_id' => 4],
            ['comment_id' => 90, 'topic_id' => 1],
            ['comment_id' => 90, 'topic_id' => 5],
            ['comment_id' => 90, 'topic_id' => 2],
            // New comment;
            ['comment_id' => 91, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 92, 'topic_id' => 1],
            ['comment_id' => 92, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 93, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 94, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 95, 'topic_id' => 1],
            ['comment_id' => 95, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 96, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 97, 'topic_id' => 1],
            // New comment;
            ['comment_id' => 98, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 99, 'topic_id' => 1],
            // New comment;
            ['comment_id' => 100, 'topic_id' => 1],
            ['comment_id' => 100, 'topic_id' => 4],
            // New comment;
            ['comment_id' => 101, 'topic_id' => 6],
            ['comment_id' => 101, 'topic_id' => 4],
            // New comment;
            ['comment_id' => 102, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 103, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 104, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 105, 'topic_id' => 1],
            // New comment;
            ['comment_id' => 106, 'topic_id' => 1],
            ['comment_id' => 106, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 107, 'topic_id' => 6],
            // New comment;
            ['comment_id' => 108, 'topic_id' => 6],
            // New comment;
            ['comment_id' => 109, 'topic_id' => 6],
            // New comment;
            ['comment_id' => 110, 'topic_id' => 6],
            // New comment;
            ['comment_id' => 111, 'topic_id' => 6],
            // New comment;
            ['comment_id' => 112, 'topic_id' => 6],
            // New comment;
            ['comment_id' => 113, 'topic_id' => 6],
            // New comment;
            ['comment_id' => 114, 'topic_id' => 6],
            // New comment;
            ['comment_id' => 115, 'topic_id' => 6],
            // New comment;
            ['comment_id' => 116, 'topic_id' => 6],
            // New comment;
            ['comment_id' => 117, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 118, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 119, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 120, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 121, 'topic_id' => 6],
            ['comment_id' => 121, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 122, 'topic_id' => 6],
            // New comment;
            ['comment_id' => 123, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 124, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 125, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 126, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 127, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 130, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 131, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 132, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 133, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 134, 'topic_id' => 6],
            // New comment;
            ['comment_id' => 135, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 136, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 137, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 138, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 139, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 140, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 141, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 142, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 143, 'topic_id' => 6],
            ['comment_id' => 143, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 144, 'topic_id' => 5],
            // New comment;
            ['comment_id' => 145, 'topic_id' => 5],
        ];
        
        
        

        foreach ($associations_EU as $association) {
            DB::table('comment_topic')->updateOrInsert(
                [
                    'comment_id' => $association['comment_id'],
                    'topic_id' => $association['topic_id']
                ],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
