<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY');
    }

    public function generateSubtopics($topic)
    {
        Log::info('generateSubtopics function called', ['topic' => $topic]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => "Toto je rozhovor o sociálnych problémoch a ich podtémach."],
                    ['role' => 'user', 'content' => "Vstupná téma: Dostanete tému súvisiacu so sociálnymi otázkami. Témy sú široké oblasti, ktoré vyžadujú nuancované rozčlenenie pre ich plné preskúmanie. Úloha: Vašou úlohou je identifikovať a zoznamovať sedem podtém, ktoré pokrývajú rôzne dimenzie danej témy. Tieto podtémy by mali poskytnúť komplexný prehľad o hlavných problémoch, perspektívach a debatách, ktoré obklopujú tému. Štruktúra podtém: Podtémy musia zahŕňať šesť špecifických oblastí relevantných pre tému a jednu všeobecnú kategóriu. Všeobecná kategória, označená ako 'Ostatné' je navrhnutá tak, aby zahŕňala akékoľvek ďalšie aspekty alebo perspektívy, ktoré sa nezmestia do ostatných šiestich kategórií. Jazyk: Témy a podtémy budú poskytnuté v češtine alebo slovenčine. Uistite sa, že podtémy sú presne reprezentované v danom jazyku. Sú výstižné a nie dlhšie ako 3 slová! Formát výstupu: Vaša odpoveď by mala striktne dodržiavať tento formát: Podtémy: [Podtéma 1], [Podtéma 2], [Podtéma 3], [Podtéma 4], [Podtéma 5], [Podtéma 6], Ostatné. Nahraďte [Podtéma 1] až [Podtéma 6] konkrétnymi podtémami, ktoré identifikujete pre tému. Striktne ich musí byť dokopy 6 + Ostatné. Príklady: Na ilustráciu, ak téma je 'Názor na členstvo v Európskej únii (EÚ),' váš výstup by mohol vyzerať takto: Podtémy: Ekonomické výhody, Politický vplyv a suverenita, Byrokracia a legislatíva, Migrácia a voľný pohyb, Obavy a kritika EÚ, Pozitívny postoj k EÚ, Ostatné. Pre tému ako 'Elektrické autá' príkladný výstup by mohol byť: Podtémy: Ekonomika, Logistika, Ekologický dopad, Kultúrne zmeny, Podpora elektromobilov, Opozícia voči elektromobilom, Ostatné. Koniec pokynov Tema: '$topic'"]
                ]
            ]);

            Log::info('API request sent', ['response' => $response->body()]);

            // Additional processing and response handling here if needed
            return $response;
            
        } catch (\Exception $e) {
            Log::error('Error in generateSubtopics function', ['message' => $e->getMessage()]);
            // Handle the exception as needed
        }
    }
   
   
#   Komentáre :
    #public function assigneSubtopics($post,$topics,$comments)
    #{
    #    Log::info('assigneSubtopics function called', ['post' => $post]);
    #    Log::info('assigneSubtopics function called', ['topics' => $topics]);
    #    Log::info('assigneSubtopics function called', ['comments' => $comments]);
    #
    #    // Každý komentár bude formátovaný s prázdny riadkom na konci.
    #    $formattedComments = array_map(function($comment) {
    #        return $comment . "\n";
    #    }, $comments);
    #
    #    // Všetky komentáre budú spojené do jedného reťazca, oddelené prázdny riadkom.
    #    $commentsString = implode("\n", $formattedComments);
    #
    #    $topicsString = implode(',', $topics); // Vytvorí reťazec tém oddelených čiarkami
    #
    #    Log::info('assigneSubtopics function called', ['commentsString' => $commentsString]);
    #    Log::info('assigneSubtopics function called', ['topicsString' => $topicsString]);
#
    #    try {
    #        $response = Http::withHeaders([
    #            'Authorization' => 'Bearer ' . $this->apiKey,
    #            'Content-Type' => 'application/json',
    #        ])->post('https://api.openai.com/v1/chat/completions', [
    #            'model' => 'gpt-3.5-turbo',
    #            'messages' => [
    #                ['role' => 'system', 'content' => "Toto je rozhovor o sociálnych problémoch a priraden9 ich podtéma."],
    #                ['role' => 'user', 'content' => "Inštrukcie: Pre danú sociálnu tému budú komentáre analyzované a kategorizované do vopred definovaných podtém. Tento proces zahŕňa identifikáciu a priradenie relevantných značiek ku každému komentáru, ktoré odzrkadľujú jeho obsah vo vzťahu k hlavnej téme a špecifikovaným podtémam.Hlavná téma: '$post'Podtémy: '$topicsString'Formát odpovede: Pre každý komentár by odpoveď mala obsahovať číslo komentára presne vo formáte v akom je pred komentárom nasledované 'TAGY:' a zoznamom priradených značiek oddelených čiarkou.Poznámka: Je dôležité brať do úvahy nuance v komentároch pre správne priradenie značiek. Ak komentár spadá do viacerých podtém, priraďte všetky relevantné značky. Každý komentár začína číslom a končí prázdny riadkom, čo uľahčuje jeho identifikáciu a spracovanie.Príklad:Komentár: '111. Bez EÚ by ekonomická nerovnosť bola oveľa väčšia a migrácia by tiež bola problémom. Toto nie sú problémy, ktoré vytvorila existencia EÚ.'Odpoveď: 111. TAGY: Ekonomický prínos EÚ, Migrácia a voľný pohyb, Pozitívny postoj k EÚ Komentáre :$comments"]
    #            ]
    #        ]);
#
    #        Log::info('API request sent', ['response' => $response->body()]);
#
    #        // Additional processing and response handling here if needed
    #        return $response;
#
    #    } catch (\Exception $e) {
    #        Log::error('Error in generateSubtopics function', ['message' => $e->getMessage()]);
    #        // Handle the exception as needed
    #    }
    #}
    public function assigneSubtopics($post,$topics,$comments)
    {
        Log::info('assigneSubtopics function called', ['post' => $post]);
        Log::info('assigneSubtopics function called', ['topics' => $topics]);
        Log::info('assigneSubtopics function called', ['comments' => $comments]);

        // No need to format comments with an empty line after it for API request
        // Removed the part that formats comments with "\n"

        $topicsString = implode(',', $topics); // This creates a string of topics separated by commas

        try {
            $messages = [
                ['role' => 'system', 'content' => "Toto je rozhovor o sociálnych problémoch a priradení ich podtéma."],
                ['role' => 'user', 'content' => "Inštrukcie: Pre danú sociálnu tému budú komentáre analyzované a kategorizované do vopred definovaných podtém. Tento proces zahŕňa identifikáciu a priradenie relevantných značiek ku každému komentáru, ktoré odzrkadľujú jeho obsah vo vzťahu k hlavnej téme a špecifikovaným podtémam. Hlavná téma: '$post' Podtémy: '$topicsString' Formát odpovede: Pre každý komentár by odpoveď mala obsahovať číslo komentára presne vo formáte v akom je pred komentárom nasledované 'TAGY:' a zoznamom priradených značiek oddelených čiarkou toto je ve2mi d;le6it0 pou69vaj iba toto slovo.Nepíš nič iné ani komentár dodržuj [číslo komentára]. TAGY: . Poznámka: Je dôležité brať do úvahy nuance v komentároch pre správne priradenie značiek. Ak komentár spadá do viacerých podtém, priraďte všetky relevantné značky. Každý komentár začína číslom a končí prázdny riadkom, čo uľahčuje jeho identifikáciu a spracovanie."]
            ];

            // Adding each comment as a separate message
            foreach ($comments as $index => $comment) {
                $messages[] = ['role' => 'user', 'content' => ($index + 1) . ". " . $comment];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => $messages
            ]);

            return $response;

        } catch (\Exception $e) {
            Log::error('Error in assigneSubtopics function', ['message' => $e->getMessage()]);
        }
    }
}

?>