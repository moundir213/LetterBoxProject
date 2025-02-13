<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RegistrationControllerTest extends WebTestCase
{
    // Test the registration page is accessible
    public function testRegistrationPageIsAccessible(): void
    {
        $client = static::createClient(); // Create a client to simulate the request
        $client->request('GET', '/register'); // Simulate a GET request to /register

        // Assert that the status code is 200, meaning the page is accessible
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    // Test registration form submission with valid data
    public function testValidRegistrationForm(): void
    {
        $client = static::createClient();

        // Simulate filling the registration form with valid data
        $client->request('GET', '/register');
        $client->submitForm('Register', [
            'registration_form[email]' => 'test@example.com',
            'registration_form[plainPassword][first]' => 'password123',
            'registration_form[plainPassword][second]' => 'password123',
        ]);

        // Check if the form was submitted and the user is redirected (status code 302)
        $this->assertResponseRedirects('/'); // Assuming redirection to home page after success
    }

    // Test registration form submission with invalid password
    public function testInvalidPasswordOnRegistration(): void
    {
        $client = static::createClient();

        // Simulate filling the registration form with an invalid password
        $client->request('GET', '/register');
        $client->submitForm('Register', [
            'registration_form[email]' => 'test@example.com',
            'registration_form[plainPassword][first]' => 'password123',
            'registration_form[plainPassword][second]' => 'wrongpassword',
        ]);

        // Assert that the form is not valid and errors are present
        $this->assertResponseStatusCodeSame(Response::HTTP_OK); // The form is re-rendered
        $this->assertSelectorTextContains('.form-error-message', 'The password fields must match.');
    }

    // Test email verification page is accessible for authenticated user
    public function testVerifyEmailPageIsAccessible(): void
    {
        $client = static::createClient();

        // Simulate logging in a user (you might want to create a user first)
        $client->loginUser($this->createTestUser()); // Create a test user method should be implemented

        // Simulate a GET request to the email verification page
        $client->request('GET', '/verify/email');

        // Assert that the page is accessible
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    // Test if an unverified email is correctly handled
    public function testEmailVerificationError(): void
    {
        $client = static::createClient();

        // Simulate sending a request with an invalid verification token (you should handle this in the test)
        $client->request('GET', '/verify/email?token=invalidToken');

        // Assert that an error message is shown for an invalid token
        $this->assertResponseRedirects('/register');
        $this->assertFlashMessageContains('verify_email_error', 'The email verification link is invalid.');
    }

    // Helper method to create a test user for login in tests
    private function createTestUser(): User
    {
        $user = new User();
        $user->setEmail('testuser@example.com');
        $user->setPassword('password123'); // You can use a password encoder here if necessary
        $user->setIsVerified(true); // Mark as verified for login purposes

        // Save user to the database (You could use an EntityManager or a mock for testing)
        // This step can be customized according to your test database setup

        return $user;
    }
}
