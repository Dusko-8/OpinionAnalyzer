<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $titles = [
            'Aký máte názor na členstvo v EU ?',
            'Aký máte názor na interupcie ?',
            'Aký máte názor na elektromobili ?'
        ];

        foreach ($titles as $title) {
            Post::firstOrCreate([
                'title' => $title,
                // You should modify the following fields as per your requirements
                'subreddit_id' => 1, // Example subreddit ID
                'post_url' => 'http://example.com/post/' . str_replace(' ', '_', $title),
                'date_scraped' => now()
            ]);
        }
    }
}
