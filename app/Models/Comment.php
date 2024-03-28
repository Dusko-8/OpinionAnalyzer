<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'topic_id',
        'comment_text',
        'date_scraped'
    ];

    protected $primaryKey = 'comment_id';
    protected $table = 'comments';
    
    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
    
    public function topics()
    {
        return $this->belongsToMany(Topic::class, 'comment_topic', 'comment_id', 'topic_id');
    }
}
