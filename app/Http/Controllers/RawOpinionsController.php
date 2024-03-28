<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\Log;
class RawOpinionsController extends Controller
{
    public function index()
    {
        #// Eager load both topics and comments with the posts
        $posts = Post::with(['topics'])->get();
        #foreach ($posts as $post) {
        #    Log::info('Comments for post ' . $post->id, $post->comments->toArray());
        #}
        return view('rawopinions', compact('posts'));
    }

    public function getComments(Request $request)
    {
        // Assuming you're passing 'postId' as a query parameter in the URL
        $postId = $request->query('postid');

        // Log the post ID
        Log::info('Fetching comments for post ID: ' . $postId);

        // Fetch the comments for the given post
        $comments = Post::findOrFail($postId)->comments;

        // Get the last 5 comment IDs
        $lastFiveCommentIds = $comments->reverse()->take(5)->pluck('comment_id');
        
        // Log the last 5 comment IDs
        Log::info('Last 5 comment IDs: ' . $lastFiveCommentIds->toJson());
        
        // Return the comments as a JSON response
        return response()->json($comments);
    }

    public function deleteComment(Request $request)
    {
        $commentId = $request->query('commentId');

        // Log the comment ID before attempting to find and delete it
        Log::info('Attempting to delete comment with ID: ' . $commentId);

        try {
            $comment = Comment::findOrFail($commentId);
            $comment->delete();

            // Log a success message after the comment has been successfully deleted
            Log::info('Comment with ID: ' . $commentId . ' was successfully deleted.');

            return response()->json(['success' => true, 'message' => 'Comment deleted successfully']);
        } catch (\Exception $e) {
            // Log the error message if something goes wrong
            Log::error('Error deleting comment with ID: ' . $commentId . '. Error: ' . $e->getMessage());

            // Return an error response
            return response()->json(['success' => false, 'message' => 'Error deleting comment.']);
        }
    }
    //public function editComment(commentId) {
    //    // Logic to handle comment editing
    //}
    //
    //public function deleteComment(commentId) {
    //    // Logic to handle comment deletion
    //}

}
