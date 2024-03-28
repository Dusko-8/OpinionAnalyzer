<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Services\PostService;
use App\Models\Post;
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

        
        // Pass the social question as an argument to the Python script
         // Pass the social question, email, and password as arguments to the Python script
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
        echo $output;
        // Try to decode the output as JSON
        $decodedOutput = json_decode($output, true);
    
        if (json_last_error() === JSON_ERROR_NONE) {
            // If the output was valid JSON, return it as a JSON response
            return response()->json($decodedOutput);
        } else {
            // If the output was not valid JSON, return it as plain text
            // Ensure that the response is properly encoded in UTF-8
            return response($output, 200, ['Content-Type' => 'text/plain; charset=utf-8']);
        }
    }

    public function storePost(Request $request)
    {
        $data = $this->validateRequest($request);
        $post = PostService::createPost($data);
        return response()->json($post, 201);
    }

    protected function validateRequest(Request $request)
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            // other validation rules
        ]);
    }
    #public function checkPostExists(Request $request)
    #{
    #    // You can adjust the request inputs based on your form data
    #    $title = $request->input('title');
    #    $postUrl = $request->input('post_url');
#
    #    // Check if a post with the same title or URL already exists
    #    $postExists = Post::where('title', $title)
    #                    ->orWhere('post_url', $postUrl)
    #                    ->exists();
#
    #    if ($postExists) {
    #        return response()->json(['status' => 'exists'], 200);
    #    } else {
    #        return response()->json(['status' => 'not exists'], 200);
    #    }
    #}
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
