<?php

namespace App\Service;

use App\Service\DescriptionService;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MistralAiDescriptionService implements DescriptionService
{
    private HttpClientInterface $httpClient;

    private string $url = "https://api.mistral.ai/v1/chat/completions";
    private String $apiKey = "ID14ShA91efzNQgeCciFscSQ0xHf9PqA";

    private string $shortPromptFormatFR = '{
    "model": "mistral-large-latest",
    "messages": [
         {"role":"user","content": "Donne moi la description en 2 lignes de l\'anime $ANIME_NAME"}
    ]
    }';

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    private function getShortPrompt(string $anime_name) {
        return str_replace('$ANIME_NAME', $anime_name, $this->shortPromptFormatFR);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function getShortDescriptionForAnime(string $anime_name): string
    {
        $response = $this->httpClient->request('POST', $this->url, [
            'headers' => [
                "Authorization" => "Bearer " . $this->apiKey,
                "Content-Type" => "application/json",
                "Accept" => "application/json"
            ],
            'body' => $this->getShortPrompt($anime_name)
        ]);
        try {
            return $response->toArray()['choices'][0]['message']['content'];
        } catch (ClientExceptionInterface|RedirectionExceptionInterface|DecodingExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
            return 'ça marche pas !';
        }
    }
}