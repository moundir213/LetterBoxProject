<?php

namespace App\Tests\Entity;

use App\Entity\Anime;
use App\Entity\User;
use App\Entity\StarredAnime;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\Collection;

class AnimeTest extends TestCase
{
    /**
     * Test setting and getting the anime title.
     */
    public function testAnimeTitle(): void
    {
        $anime = new Anime();
        $anime->setTitle('Attack on Titan');

        // Ensure that the title was correctly set and retrieved
        $this->assertSame('Attack on Titan', $anime->getTitle());
    }

    /**
     * Test setting and getting the anime picture.
     */
    public function testAnimePicture(): void
    {
        $anime = new Anime();
        $anime->setPicture('attack_on_titan.jpg');

        // Ensure that the picture path was correctly set and retrieved
        $this->assertSame('attack_on_titan.jpg', $anime->getPicture());
    }

    /**
     * Test adding and removing users who liked the anime.
     */
    public function testUsersLiking(): void
    {
        $anime = new Anime();
        $user = new User();

        // Ensure that the collection is initially empty
        $this->assertInstanceOf(Collection::class, $anime->getUsersLiking());
        $this->assertCount(0, $anime->getUsersLiking());

        // Add a user who likes the anime
        $anime->addUsersLiking($user);

        // Ensure that the user was added
        $this->assertCount(1, $anime->getUsersLiking());
        $this->assertTrue($anime->getUsersLiking()->contains($user));

        // Remove the user who liked the anime
        $anime->removeUsersLiking($user);

        // Ensure that the user was removed
        $this->assertCount(0, $anime->getUsersLiking());
        $this->assertFalse($anime->getUsersLiking()->contains($user));
    }

    /**
     * Test adding and removing starred ratings for an anime.
     */
    public function testUserStars(): void
    {
        $anime = new Anime();
        $user = new User();
        $starredAnime = new StarredAnime();
        $starredAnime->setAnime($anime);
        $starredAnime->setUser($user);
        $starredAnime->setStars(5);

        // Ensure that the collection is initially empty
        $this->assertInstanceOf(Collection::class, $anime->getUserStars());
        $this->assertCount(0, $anime->getUserStars());

        // Add a starred rating for the anime
        $anime->addUserStar($starredAnime);

        // Ensure that the rating was added
        $this->assertCount(1, $anime->getUserStars());
        $this->assertTrue($anime->getUserStars()->contains($starredAnime));

        // Remove the starred rating
        $anime->removeUserStar($starredAnime);

        // Ensure that the rating was removed
        $this->assertCount(0, $anime->getUserStars());
        $this->assertFalse($anime->getUserStars()->contains($starredAnime));
    }

    /**
     * Test retrieving the number of stars given by a specific user.
     */
    public function testGetStarsOfUser(): void
    {
        $anime = new Anime();
        $user = new User();
        $starredAnime = new StarredAnime();
        $starredAnime->setAnime($anime);
        $starredAnime->setUser($user);
        $starredAnime->setStars(4);

        // Add a starred rating to the anime
        $anime->addUserStar($starredAnime);

        // Ensure the correct number of stars is returned for the user
        $this->assertSame(4, $anime->getStarsOfUser($user));

        // Ensure it returns -1 for a user who has not rated the anime
        $this->assertSame(-1, $anime->getStarsOfUser(new User()));

        // Ensure it returns -1 when passing null
        $this->assertSame(-1, $anime->getStarsOfUser(null));
    }
}
