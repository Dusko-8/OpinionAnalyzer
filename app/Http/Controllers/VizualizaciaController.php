<?php

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

    public function show(Request $request)
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
                    #Log::info('Count I want', $topic->comments);
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
}

