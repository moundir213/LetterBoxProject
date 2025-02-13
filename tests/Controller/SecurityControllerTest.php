<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    // Test login page is accessible
    public function testLoginPageIsAccessible(): void
    {
        $client = static::createClient(); // Create a client to simulate the request
        $client->request('GET', '/login'); // Simulate a GET request to the login page

        // Assert that the login page is accessible with a 200 status code
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    // Test login form with valid credentials
    public function testValidLogin(): void
    {
        $client = static::createClient();

        // Simulate submitting the login form with valid data
        $client->request('GET', '/login');
        $client->submitForm('Login', [
            'username' => 'testuser@example.com',
            'password' => 'password123',
        ]);

        // Check if the user is redirected after a successful login (usually to the home page)
        $this->assertResponseRedirects('/');
    }

    // Test login form with invalid credentials
    public function testInvalidLogin(): void
    {
        $client = static::createClient();

        // Simulate submitting the login form with invalid credentials
        $client->request('GET', '/login');
        $client->submitForm('Login', [
            'username' => 'wronguser@example.com',
            'password' => 'wrongpassword',
        ]);

        // Assert that an error is displayed and the page is re-rendered (status code 200)
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('.form-error-message', 'Invalid credentials.');
    }

    // Test the logout functionality
    public function testLogout(): void
    {
        $client = static::createClient();

        // Simulate a request to the logout route
        $client->request('GET', '/logout');

        // As the logout route does not return any response, we just check if it's working
        // Symfony will redirect the user to the login page or some other route based on the configuration
        $this->assertResponseRedirects('/login');
    }
}
