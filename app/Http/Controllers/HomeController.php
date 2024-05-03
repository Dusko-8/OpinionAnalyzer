<?php
/************************************************************
 * Author: Dušan Slúka
 *
 * Description: Contains server side functions for geting 
 * comments window.
 ************************************************************/
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Services\PostService;
use Illuminate\Support\Facades\DB;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function scrapeReddit(Request $request)
    {
        // Retrieve query parameters from the request
        $question = $request->query('question');
        $language = $request->query('language');
        
        Log::info('Scraping Reddit for question:', ['question' => $question]);

        // Ensure that your Python script can handle these arguments properly
        $process = new Process([
            'python', 
            'P:\OpinionAnalyzer\scripts\scrape-reddit.py', 
            $question, // Pass the question as an argument
            $language, // Pass the language as an argument
        ], env: [
            'SYSTEMROOT' => getenv('SYSTEMROOT'),
            'PATH' => getenv("PATH"),
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();
        $decodedOutput = json_decode($output, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return response()->json($decodedOutput);
        } else {
            echo("Not JSON");
            return response($output, 200, ['Content-Type' => 'text/plain; charset=utf-8']);
        }
    }

    public function scrapeFacebook(Request $request)
    {
        // Validate the request to ensure all required parameters are present
        $validatedData = $request->validate([
            'topic' => 'required', // social question must be provided
            'email' => 'required|email', // email must be provided and should be a valid email
            'password' => 'required' // password must be provided
        ]);

        // Assign the validated data to variables
        $socialQuestion = $validatedData['topic'];
        $email = $validatedData['email'];
        $password = $validatedData['password'];

        $process = new Process([
            'python',
            'P:\OpinionAnalyzer\scripts\scrape-facebook.py',
            $socialQuestion, // Pass the social question parameter
            $email, // Pass the email parameter
            $password // Pass the password parameter
        ], env: [
            'SYSTEMROOT' => getenv('SYSTEMROOT'),
            'PATH' => getenv("PATH")
        ]);
    
        $process->run();
    
        // Check if the process was successful
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    
        $output = $process->getOutput();
        Log::info('Outputs:', ['Outputs' => $output]);
        
        // Try to decode the output as JSON
        $decodedOutput = json_decode($output, true);
        Log::info('decodedOutput:', ['decodedOutput' => $decodedOutput]);

        if (json_last_error() === JSON_ERROR_NONE) {
            return response()->json($decodedOutput);
        } else {
            echo("Not JSON");
            return response($output, 200, ['Content-Type' => 'text/plain; charset=utf-8']);
        }
    }

    public function storePost2(Request $request)
    {

        Log::info('0');

        // Validate the incoming request data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'comments' => 'sometimes|array',
            'comments.*' => 'string|max:1000', // Example validation rule for comments
        ]);

        Log::info('1');

        // Create the post
        $post = Post::create([
            'title' => $validatedData['title'],
        ]);

        Log::info('2');

        // Check if there are comments to save
        if (isset($validatedData['comments']) && !empty($validatedData['comments'])) {
            foreach ($validatedData['comments'] as $commentText) {
                // Create each comment using the Comment model directly

                Log::info('Saving comment: ', ['comment' => $commentText]);

                Comment::create([
                    'post_id' => $post->post_id,
                    'comment_text' => $commentText,
                    'date_scraped' => now(),
                ]);
            }
        }

        // Optionally return the post and its comments in the response
        return response()->json($post->load('comments'), 201);
    }

    public function storePost(Request $request)
    {
        Log::info('0');

        // Validate the incoming request data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'comments' => 'sometimes|array',
            'comments.*' => 'string|max:1000', // Example validation rule for comments
        ]);

        Log::info('1');

        // Create the post
        $post = Post::create([
            'title' => $validatedData['title'],
        ]);

        Log::info('2');

        // Check if there are comments to save and use a transaction to ensure all operations are atomic
        if (isset($validatedData['comments']) && !empty($validatedData['comments'])) {
            DB::transaction(function () use ($validatedData, $post) {
                foreach ($validatedData['comments'] as $commentText) {
                    Log::info('Saving comment:', ['comment' => $commentText]); // Optional: Log each comment being saved

                    Comment::create([
                        'post_id' => $post->post_id,
                        'comment_text' => $commentText,
                        'date_scraped' => now(),
                        // Add 'topic_id' if necessary and available
                    ]);
                }
            });
        }

        // Optionally return the post and its comments in the response
        return response()->json($post->load('comments'), 201);
    }

    public function checkPostExists(Request $request)
    {
        $title = $request->title; // Assuming you're sending the title as a parameter

        // Use the method from the Post model
        $exists = Post::existsWithTitle($title);

        // Or directly without the method in the model
        // $exists = Post::where('title', $title)->exists();

        return response()->json([
            'exists' => $exists,
        ]);
    }
}
