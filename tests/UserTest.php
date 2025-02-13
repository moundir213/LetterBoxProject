<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Anime;
use App\Entity\StarredAnime;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
     * Test setting and getting the email of the user.
     */
    public function testSetAndGetEmail(): void
    {
        $user = new User();

        // Set email for the user
        $user->setEmail('test@example.com');

        // Ensure the email was correctly set and retrieved
        $this->assertSame('test@example.com', $user->getEmail());
    }

    /**
     * Test setting and getting the user roles.
     */
    public function testSetAndGetRoles(): void
    {
        $user = new User();

        // Set roles for the user
        $user->setRoles(['ROLE_ADMIN']);

        // Ensure the roles are correctly set and retrieved
        $this->assertSame(['ROLE_ADMIN'], $user->getRoles());

        // Ensure that 'ROLE_USER' is added by default
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    /**
     * Test setting and getting the password.
     */
    public function testSetAndGetPassword(): void
    {
        $user = new User();

        // Set password for the user
        $user->setPassword('password123');

        // Ensure the password was correctly set and retrieved
        $this->assertSame('password123', $user->getPassword());
    }

    /**
     * Test adding and removing liked animes.
     */
    public function testLikedAnimes(): void
    {
        $user = new User();
        $anime = new Anime();

        // Add anime to likedAnimes
        $user->addLikedAnime($anime);

        // Ensure the anime was added to likedAnimes
        $this->assertCount(1, $user->getLikedAnimes());
        $this->assertTrue($user->getLikedAnimes()->contains($anime));

        // Remove anime from likedAnimes
        $user->removeLikedAnime($anime);

        // Ensure the anime was removed from likedAnimes
        $this->assertCount(0, $user->getLikedAnimes());
    }

    /**
     * Test adding and removing starred animes.
     */
    public function testStarredAnimes(): void
    {
        $user = new User();
        $anime = new Anime();
        $starredAnime = new StarredAnime();

        // Set anime for the starredAnime
        $starredAnime->setAnime($anime);

        // Add starredAnime to the user
        $user->addStarredAnime($starredAnime);

        // Ensure the starredAnime was added to the user's starredAnimes
        $this->assertCount(1, $user->getStarredAnimes());
        $this->assertTrue($user->getStarredAnimes()->contains($starredAnime));

        // Remove starredAnime from the user's starredAnimes
        $user->removeStarredAnime($starredAnime);

        // Ensure the starredAnime was removed from the user's starredAnimes
        $this->assertCount(0, $user->getStarredAnimes());
    }

    /**
     * Test checking if the user is verified.
     */
    public function testIsVerified(): void
    {
        $user = new User();

        // Ensure that a new user is not verified by default
        $this->assertFalse($user->isVerified());

        // Set the user to be verified
        $user->setIsVerified(true);

        // Ensure the user is now verified
        $this->assertTrue($user->isVerified());
    }

    /**
     * Test the default behavior of the User entity.
     */
    public function testDefaultValues(): void
    {
        $user = new User();

        // Ensure that by default, the email and password are null
        $this->assertNull($user->getEmail());
        $this->assertNull($user->getPassword());

        // Ensure the roles array is empty by default
        $this->assertEmpty($user->getRoles());

        // Ensure the user is not verified by default
        $this->assertFalse($user->isVerified());
    }
}
