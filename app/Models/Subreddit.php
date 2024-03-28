<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subreddit extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'language']; // Add 'language' here

    // Specify your table name if it's not the plural of your model name
    protected $table = 'subreddits';

    // Primary key (change if not using the default 'id' field)
    protected $primaryKey = 'subreddit_id';

    // If you're not using Laravel's default timestamps (created_at and updated_at),
    // or if you want to disable them, you can set this to false
    public $timestamps = true;

}
