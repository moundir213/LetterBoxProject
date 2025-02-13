<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class HomeControllerTest extends WebTestCase
{
    /**
     * Tests the home page redirection based on authentication status.
     */
    public function testHomePageRedirects(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        // Check if the user is redirected to the login page (302 Redirect)
        self::assertResponseRedirects('/login');
    }

    /**
     * Tests redirection for authenticated users.
     */
    public function testAuthenticatedUserRedirectsToAnimeList(): void
    {
        $client = static::createClient();
        $client->loginUser($this->createMockUser()); // Simulating a logged-in user
        $client->request('GET', '/');

        // Check if the user is redirected to the anime list page (302 Redirect)
        self::assertResponseRedirects('/anime');
    }

    /**
     * Helper method to create a mock authenticated user.
     */
    private function createMockUser()
    {
        $user = new \App\Entity\User();
        $user->setEmail('test@example.com');
        $user->setPassword('password123'); // Password encoding is not required for tests
        return $user;
    }
}
