<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $table = 'topics';

    // Indicate that the primary key is 'topic_id' not 'id'
    protected $primaryKey = 'topic_id';

    // If your primary key is not auto-incrementing or not numeric, you should also set these:
    // public $incrementing = false;
    // protected $keyType = 'string';

    protected $fillable = ['topic_name'];

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_topic', 'topic_id', 'post_id');
    }

    public function comments()
    {
        // Assuming 'comment_topic' is the intermediate table,
        // 'topic_id' is the foreign key in the intermediate table for the Topic model,
        // and 'comment_id' is the foreign key for the Comment model.
        return $this->belongsToMany(Comment::class, 'comment_topic', 'topic_id', 'comment_id');
    }
}
