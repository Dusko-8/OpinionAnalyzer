<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id('post_id');
            $table->string('title');
            $table->foreignId('subreddit_id')->nullable()->constrained('subreddits', 'subreddit_id'); // Make subreddit_id nullable
            $table->string('post_url')->unique()->nullable(); // Make post_url nullable and unique
            $table->dateTime('date_scraped')->nullable(); // Make date_scraped nullable
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
