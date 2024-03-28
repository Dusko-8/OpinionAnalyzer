<?php

use App\Models\Post;
use App\Models\Comment;
use App\Models\Subreddit;
use App\Models\PostTopic;
use App\Models\Topic;

class PostService
{
    public static function createPost($data)
    {
        $post = new Post();
        $post->title = $data['title'];
        // other fields
        $post->save();

        // Handle other related operations, e.g., saving comments
        foreach ($data['comments'] as $commentText) {
            $comment = new Comment();
            $comment->post_id = $post->id;
            $comment->comment_text = $commentText;
            $comment->save();
        }

        return $post;
    }
}

