<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Topic;

class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $topics = [

            'Ekonomický prínos EU', 'Politický vplyv a suverenita', 'Byrokracia a legislatíva',
            'Migrácia a voľný pohyb', 'Obavy a kritika EÚ', 'Pozitívny postoj k EÚ', 'Ostatné',

            'Ekonomika', 'Logistika', 'Ekologické', 'Kultúra',
            'Podpora elektromobilov', 'Nepodpora elektromobilov', 'Ostatné',

            'Právo výberu', 'Právo Plodu', 'Osobná Skúsenosť', 'Morálka/Etika',
            'Náboženský Pohľad', 'Zdravotné Dôvody', 'Ostatné'
        ];

        foreach ($topics as $topicName) {
            Topic::firstOrCreate(['topic_name' => $topicName]);
        }
    }
}
