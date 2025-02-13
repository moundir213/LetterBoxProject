<?php

namespace App\Tests\Entity;

use App\Entity\StarredAnime;
use App\Entity\User;
use App\Entity\Anime;
use PHPUnit\Framework\TestCase;

class StarredAnimeTest extends TestCase
{
    /**
     * Test setting and getting the user who rated the anime.
     */
    public function testSetAndGetUser(): void
    {
        $starredAnime = new StarredAnime();
        $user = new User();

        // Set user for the starred anime
        $starredAnime->setUser($user);

        // Ensure the user was correctly set and retrieved
        $this->assertSame($user, $starredAnime->getUser());
    }

    /**
     * Test setting and getting the anime that was rated.
     */
    public function testSetAndGetAnime(): void
    {
        $starredAnime = new StarredAnime();
        $anime = new Anime();

        // Set anime for the starred anime entity
        $starredAnime->setAnime($anime);

        // Ensure the anime was correctly set and retrieved
        $this->assertSame($anime, $starredAnime->getAnime());
    }

    /**
     * Test setting and getting the number of stars given to an anime.
     */
    public function testSetAndGetStars(): void
    {
        $starredAnime = new StarredAnime();

        // Set the stars rating
        $starredAnime->setStars(5);

        // Ensure the stars were correctly set and retrieved
        $this->assertSame(5, $starredAnime->getStars());

        // Ensure that null is handled correctly
        $starredAnime->setStars(null);
        $this->assertNull($starredAnime->getStars());
    }

    /**
     * Test that the default values are null before any setter is called.
     */
    public function testDefaultValues(): void
    {
        $starredAnime = new StarredAnime();

        // Ensure that by default, all properties are null
        $this->assertNull($starredAnime->getId());
        $this->assertNull($starredAnime->getUser());
        $this->assertNull($starredAnime->getAnime());
        $this->assertNull($starredAnime->getStars());
    }
}
