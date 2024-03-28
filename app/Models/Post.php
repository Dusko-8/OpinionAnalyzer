<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    // Define the table if it's not the plural of the model name (optional here)
    protected $table = 'posts';

    // Mass assignable attributes
    protected $fillable = ['title', 'subreddit_id', 'post_url', 'date_scraped'];

    protected $primaryKey = 'post_id';
    // Defining the relationship with Subreddit
    public function subreddit()
    {
        return $this->belongsTo(Subreddit::class, 'subreddit_id', 'subreddit_id');
    }

    public function topics()
    {
        return $this->belongsToMany(Topic::class, 'post_topic', 'post_id', 'topic_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id');
    }

    public static function existsWithTitle($title)
    {
        return self::where('title', $title)->exists();
    }

}
