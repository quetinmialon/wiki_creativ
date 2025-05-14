<?php

use Illuminate\Support\Facades\Http;

class SemanticSearchService
{
    public function getEmbedding(string $text): ?array
    {
        $response = Http::post('http://localhost:5000/generate', [
            'text' => $text,
        ]);

        if ($response->successful()) {

            dd($response->json()['embedding']);
            return $response->json()['embedding'];

        }

        return null; // ou throw une exception
    }

    public function searchInElasticSearch(array $embedding):string
    {
        //logical connexion do database ELS
        $result = null;
        if(!$result){
            return 'aucun résultat cohérent trouvé';
        }
        return $result;
    }
}
