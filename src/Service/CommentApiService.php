<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CommentApiService implements CommentService
{
    private string $url;
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $parameterBag)
    {
        $this->client = $client;
        $this->url = $parameterBag->get('COMMENTS_API_URL');
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getCommentsByAnime(int $id): array
    {
        $response = $this->client->request('GET', $this->url . '/comments?anime=' . $id);
        return $response->toArray()['member'];
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getCommentsByUser(int $id): array
    {
        $response = $this->client->request('GET', $this->url . '/comments?author=' . $id);
        return $response["member"]->toArray();
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function addCommentOnAnime(int $animeId, string $content, int $authorId): ResponseInterface
    {
        return $this->client->request('POST', $this->url . '/comments', [
            'headers' => [
                'accept' => 'application/ld+json',
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'anime' => $animeId,
                'content' => $content,
                'author' => $authorId
            ]
        ]);
    }
}