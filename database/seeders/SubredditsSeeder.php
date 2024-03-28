<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subreddit;

class SubredditsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subredditsSK = ['Slovakia', 'Bratislava'];
        $subredditsCZ = ['Czech'];

        foreach ($subredditsSK as $name) {
            Subreddit::firstOrCreate(['name' => $name], ['language' => 'SK']);
        }

        foreach ($subredditsCZ as $name) {
            Subreddit::firstOrCreate(['name' => $name], ['language' => 'CZ']);
        }
    }
}
