<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Topic;
use App\Models\Comment;
use App\Services\OpenAIService;

class AnalyzeController extends Controller
{
    public function index()
    {
        // Eager load both topics and comments with the posts
        $posts = Post::with(['topics', 'comments'])->get();
        return view('analyze', compact('posts'));
    }

    public function saveTopics(Request $request)
    {
        $postId = $request->input('postId');
        $topicNames = $request->input('topics');
        
        $post = Post::find($postId);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $topics = [];
        foreach ($topicNames as $name) {
            // Create new topic or find existing one
            $topic = Topic::firstOrCreate(['topic_name' => $name]);
            array_push($topics, $topic->topic_id);
        }

        // Attach topics to the post
        $post->topics()->sync($topics);

        return response()->json(['message' => 'Topics saved successfully']);
    }
    
    protected $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    public function SugestSubtopics(Request $request)
    {
        $topic = $request->input('postText');
        Log::info('Processing topic for subtopics generation:', ['topic' => $topic]); // Log the input topic
        try {
            // Call the generateSubtopics function and store the response
            $response = $this->openAIService->generateSubtopics($topic);
                    
            // Log the full response to verify it's received correctly
            Log::info('Full API response:', ['responseBody' => $response->body()]);
                    
            // Decode the JSON response into an array
            $responseArray = json_decode($response->body(), true);
                    
            // Ensure the responseArray is not null and has the expected structure before accessing
            if (is_array($responseArray) && isset($responseArray['choices'][0]['message']['content'])) {
                // Access the message content directly from the decoded array
                $assistantMessage = $responseArray['choices'][0]['message']['content'];
            
                // Log the extracted message to verify it's correctly obtained
                Log::info('Extracted assistant message', ['assistantMessage' => $assistantMessage]);
            } else {
                // Log an error or handle the case where the response does not have the expected structure
                Log::error('Unexpected API response structure or null response array', ['responseArray' => $responseArray]);
            }
            

            // Use the correct variable here, which contains the actual text
            $keyword = "Podtémy:";
            $startPos = strpos($assistantMessage, $keyword);
        
            if ($startPos !== false) {
                // Adjusted to use the assistantMessage for substr
                $subtopicsText = substr($assistantMessage, $startPos + strlen($keyword));
                $words = array_map('trim', explode(',', $subtopicsText));
                if (count($words) >= 6) {
                    $firstSixWords = array_slice($words, 0, 6);
                } else {
                    throw new \Exception("Not enough subtopics found in the response.");
                }
            } else {
                throw new \Exception("Keyword 'Podtémy:' not found in the response.");
            }
        
            return response()->json([
                'success' => true,
                'subtopics' => $firstSixWords // Return the processed subtopics
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to generate subtopics:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'An error occurred while generating subtopics: ' . $e->getMessage()]);
        }
    }
    
    public function getCommentsFilteredByTopics(Request $request)
    {
        $postId = $request->input('post_id');
        $topicIds = $request->input('topic_ids', []);
        $topicsNames = Topic::whereIn('topic_id', $topicIds)->get()->pluck('topic_name', 'topic_id')->toArray();
    
        Log::info("Topic names: ", ['topicsNames' => $topicsNames]);
        Log::info("Received postId: $postId with topics: ", ['topics' => $topicIds]);
    
        try {
            $post = Post::findOrFail($postId);
    
            $notAnalyzedCount = $post->comments()->where('is_analyzed', false)->count();
    
            Log::info("Number of not analyzed comments: $notAnalyzedCount");
    
            // Adjust here to handle in chunks
            //$post->comments()->where('is_analyzed', false)->chunk(15, function ($commentsChunk) use ($postId, $topicsNames, $topicIds, $post) {
            $post->comments()
            ->where('is_analyzed', false)
            ->where('post_id', $postId) 
            ->chunkById(15, function ($commentsChunk) use ($postId, $topicsNames, $topicIds, $post) {
                $postTitle = $post->title;
                $commentsTexts = $commentsChunk->map(function ($comment) {
                    return "{$comment->comment_id}. {$comment->comment_text}";
                })->toArray();
    
                Log::info("Prepared comment texts to be passed:", $commentsTexts);
    
                $response = $this->openAIService->assigneSubtopics($postTitle, array_values($topicsNames), $commentsTexts);
    
                Log::info('Full API response:', ['responseBody' => $response->body()]);
    
                $responseArray = json_decode($response->body(), true);
    
                if (is_array($responseArray) && isset($responseArray['choices'][0]['message']['content'])) {
                    $assistantMessage = $responseArray['choices'][0]['message']['content'];
                    Log::info('Extracted assistant message', ['assistantMessage' => $assistantMessage]);
    
                    $lines = explode("\n", $assistantMessage);
                    $commentsTagsArray = [];
                    $index = 0;
    
                    foreach ($lines as $line) {
                        $tagPosition = strpos($line, "TAGY:");
                        if ($tagPosition !== false && isset($commentsChunk[$index])) {
                            $commentId = $commentsChunk[$index]->comment_id;
                            $tagsString = substr($line, $tagPosition + strlen("TAGY:"));
                            $tags = array_map('trim', explode(",", $tagsString));
                            $commentsTagsArray[$commentId] = $tags;
                            $index++;
                        }
                    }
    
                    Log::info('Mapped comment IDs to tags', ['commentsTagsArray' => $commentsTagsArray]);
    
                    $tagsCommentsMap = []; // Mapa tagov a IDčiek komentárov

                    foreach ($commentsTagsArray as $commentId => $tags) {
                        foreach ($tags as $tagName) {
                            // Pre každý tag zistíme príslušné ID témy (predpokladáme, že názvy tagov zodpovedajú názvom tém)
                            $topicId = array_search($tagName, $topicsNames); // Hľadáme názov tagu v mape tém
                            if ($topicId !== false) {
                                // Ak názov tagu zodpovedá nejakému ID témy, priradíme ID komentára k tomuto tagu
                                if (!isset($tagsCommentsMap[$topicId])) {
                                    $tagsCommentsMap[$topicId] = [];
                                }
                                $tagsCommentsMap[$topicId][] = $commentId;
                            }
                        }
                    }
                    $insertData = [];
                    foreach ($commentsTagsArray as $commentId => $tags) {
                        foreach ($tags as $tag) {
                            // The $tag is the name of the topic, use $topicsNames to find the corresponding topic_id
                            $topicId = array_search($tag, $topicsNames, true); // Use strict comparison

                            if ($topicId !== false) {
                                // Map the comment ID to the found topic_id correctly
                                $insertData[] = [
                                    'comment_id' => $commentId,
                                    'topic_id' => $topicId,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            } else {
                                // Log or handle the case where a tag name does not match any topic name
                                Log::warning("Tag name '$tag' does not match any topic name.");
                            }
                        }
                    }

                    // Log the prepared insertData for verification
                    Log::info('Prepared insertData for batch insertion:', ['insertData' => $insertData]);
                    // Perform batch insertion if insertData is not empty
                    if (!empty($insertData)) {
                        DB::table('comment_topic')->insert($insertData);
                        Log::info('Data inserted into comment_topic table', ['insertData' => $insertData]);
                        // Update the is_analyzed column for processed comments
                        $processedCommentIds = $commentsChunk->pluck('comment_id')->toArray();
                        DB::table('comments')->whereIn('comment_id', $processedCommentIds)->update(['is_analyzed' => true]);
                        Log::info('Updated is_analyzed column for comments', ['commentIds' => $processedCommentIds]);
                    } else {
                        Log::error('No data prepared for insertion into comment_topic table');
                    }

                } else {
                    Log::error('Unexpected API response structure or null response array', ['responseArray' => $responseArray]);
                }
            },'comment_id');
    
            return response()->json(['message' => 'Subtopics assignment completed for all comments.']);
    
        } catch (\Exception $e) {
            Log::error('Error retrieving comments: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while retrieving comments'], 500);
        }
    }
    
}
