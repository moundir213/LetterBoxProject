<?php

namespace App\DataFixtures;

use App\Entity\Anime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MALAnimeFixtures extends Fixture
{
    private HttpClientInterface $httpClient;
    private string $url;

    private string $clientId;

    public function __construct(HttpClientInterface $httpClient, ParameterBagInterface $parameterBag)
    {
        $this->httpClient = $httpClient;
//        $this->url = "https://api.myanimelist.net/v2";
//        $this->clientId = "fea9448bfcf02ed2c536462766165325";
         $this->url = $parameterBag->get('MAL_API_URL');
         $this->clientId = $parameterBag->get('MAL_CLIENT_ID');
    }

    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        // number 0 to 10
        for ($i = 0; $i < 10; ++$i){
            // as int
            $offset = intval($i * 10);
            $requestUrl = $this->url . 'anime/ranking?offset=' . $offset;
            $response = $this->httpClient->request('GET', $requestUrl, [
                'headers' => [
                    'X-MAL-Client-ID' => $this->clientId,
                ],
            ]);

            try {
                $data = $response->toArray()['data'];
                foreach ($data as $anime) {
                    $manager->persist((new Anime())
                        ->setTitle($anime['node']['title'])
                        ->setPicture($anime['node']['main_picture']['large']));
                }
                $manager->flush();
            } catch (Exception $e) {
                throw new Exception($response->getContent());
            }
        }
    }
}
