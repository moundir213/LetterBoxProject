<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class AnimeControllerTest extends WebTestCase
{
    /**
     * Tests the display of the anime list page.
     */
    public function testShowAllAnimes(): void
    {
        $client = static::createClient();
        $client->request('GET', '/anime');

        // Check if the request is successful (HTTP 200 OK)
        self::assertResponseIsSuccessful();

        // Check if the page contains a specific expected element (e.g., anime list title)
        self::assertSelectorTextContains('h1', 'Liste des animes');
    }

    /**
     * Tests the display of a specific anime (ID = 1 here, adjust as needed).
     */
    public function testShowAnime(): void
    {
        $client = static::createClient();
        $client->request('GET', '/anime/1');

        // Check if the anime exists (HTTP 200 OK) or not (HTTP 404 Not Found)
        if ($client->getResponse()->getStatusCode() === Response::HTTP_OK) {
            self::assertResponseIsSuccessful();
            self::assertSelectorExists('.anime-title'); // Ensure an anime title is displayed
        } else {
            self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Tests liking an anime.
     */
    public function testLikeAnime(): void
    {
        $client = static::createClient();
        $client->request('POST', '/anime/1/like');

        // If the user is not logged in, they should be redirected to the login page (HTTP 302 Found)
        if ($client->getResponse()->getStatusCode() === Response::HTTP_FOUND) {
            self::assertResponseRedirects('/login');
        } else {
            self::assertResponseIsSuccessful();
        }
    }

    /**
     * Tests adding a comment to an anime (user must be logged in).
     */
    public function testAddComment(): void
    {
        $client = static::createClient();
        $client->request('POST', '/anime/1/comment', [
            'comment_body' => 'Great anime!',
        ]);

        // If the user is not logged in, they should be redirected to the login page (HTTP 302 Found)
        if ($client->getResponse()->getStatusCode() === Response::HTTP_FOUND) {
            self::assertResponseRedirects('/login');
        } else {
            self::assertResponseRedirects('/anime/1');
        }
    }

    /**
     * Tests rating an anime (giving it a star rating).
     */
    public function testRateAnime(): void
    {
        $client = static::createClient();
        $client->request('POST', '/anime/1/rate', [
            'rating' => 5,
        ]);

        // If the user is not logged in, they should be redirected to the login page (HTTP 302 Found)
        if ($client->getResponse()->getStatusCode() === Response::HTTP_FOUND) {
            self::assertResponseRedirects('/login');
        } else {
            self::assertResponseRedirects('/anime/1');
        }
    }
}
