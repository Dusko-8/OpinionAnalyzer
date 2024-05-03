<?php
/************************************************************
 * Author: Dušan Slúka
 *
 * Description: Contains server side functions for geting 
 * visualisation window.
 ************************************************************/
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Log;

class VizualizaciaController extends Controller
{
    public function index()
    {
        $posts = Post::all()->toArray(); // Convert the collection to an array

        // Log the posts
        Log::info('Posts:', $posts);

        return view('vizualizacia', compact('posts'));
    }

    public function shows(Request $request)
    {
        $labels = []; // Initialize as empty
        $data = []; // Initialize as empty
        $posts = Post::all()->toArray(); // Retrieve all posts and convert to an array
        $id = $request->query('id');
        Log::info('Received post ID:', ['id' => $id]);

        if ($id) {
            $post = Post::with(['topics.comments'])->find($id); // Eager load topics and their comments

            if ($post) {
                foreach ($post->topics as $topic) {
                    $labels[] = $topic->topic_name; // Assuming you have a 'topic_name' field
                    //Log::info('Count I want', count($topic->comments));
                    $data[] = count($topic->comments); // Count the comments associated with the topic
                }
            }
        }

        // Log the data
        Log::info('Labels:', $labels);
        Log::info('Data:', $data);

        Log::info('Compact:', compact('labels', 'data', 'posts'));
        // Even if $id is not provided or no topics are found, $labels and $data are defined (as empty arrays).
        // Now $posts is also always defined.
        return response()->json(compact('labels', 'data', 'posts'));
    }

    public function show(Request $request)
    {
        $labels = []; // Initialize 
        $data = []; // Initialize
        $posts = Post::all()->toArray(); // Retrieve all posts and convert to an array
        $id = $request->query('id');
        Log::info('Received post ID:', ['id' => $id]);
    
        if ($id) {
            $post = Post::with(['topics.comments'])->find($id); // Eager load topics and their comments
        
            if ($post) {
                foreach ($post->topics as $topic) {
                    $topicComments = $topic->comments->where('post_id', $id); // Manual filtering
                    $labels[] = $topic->topic_name;
                    $data[] = count($topicComments);
                }
            }
        }
    
        Log::info('Labels:', $labels);
        Log::info('Data:', $data);
        Log::info('Compact:', compact('labels', 'data', 'posts'));
    
        return response()->json(compact('labels', 'data', 'posts'));
    }

    public function showComments(Request $request)
    {
        $postId = $request->query('id');

        if (!$postId) {
            return response()->json(['error' => 'No post ID provided'], 400);
        }

        $post = Post::with(['topics.comments.topics'])->find($postId);  // Adjusted to correctly include nested topics

        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        $commentsData = [];
        foreach ($post->topics as $topic) {
            foreach ($topic->comments as $comment) {
                $commentTopics = $comment->topics->pluck('topic_name')->toArray();
                $commentsData[] = [
                    'text' => $comment->comment_text,  // Make sure the field name matches your database schema
                    'topics' => $commentTopics  // Previously referred to as tags
                ];
            }
        }
        Log::info('commentsData:', $commentsData);
        return response()->json(['commentsData' => $commentsData]);
    }
}

