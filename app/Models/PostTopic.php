<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostTopic extends Model
{
    use HasFactory;
    // Set the table name explicitly
    protected $table = 'post_topic';

    // Disable timestamps if you don't need them
    public $timestamps = false;

    // Fillable fields for mass assignment
    protected $fillable = ['post_id', 'topic_id'];
}
