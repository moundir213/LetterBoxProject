<?php

namespace App\Entity;

use App\Repository\AnimeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnimeRepository::class)]
#[ORM\Table(name: 'animes')]
class Anime
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $picture = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'likedAnimes')]
    #[ORM\JoinTable(name: 'users_liked_animes')]
    private Collection $usersLiking;

    /**
     * @var Collection<int, StarredAnime>
     */
    #[ORM\OneToMany(targetEntity: StarredAnime::class, mappedBy: 'anime', orphanRemoval: true)]
    private Collection $userStars;

    public function __construct()
    {
        $this->usersLiking = new ArrayCollection();
        $this->userStars = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsersLiking(): Collection
    {
        return $this->usersLiking;
    }

    public function addUsersLiking(User $usersLiking): static
    {
        if (!$this->usersLiking->contains($usersLiking)) {
            $this->usersLiking->add($usersLiking);
            $usersLiking->addLikedAnime($this);
        }

        return $this;
    }

    public function removeUsersLiking(User $usersLiking): static
    {
        if ($this->usersLiking->removeElement($usersLiking)) {
            $usersLiking->removeLikedAnime($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, StarredAnime>
     */
    public function getUserStars(): Collection
    {
        return $this->userStars;
    }

    public function addUserStar(StarredAnime $userStar): static
    {
        if (!$this->userStars->contains($userStar)) {
            $this->userStars->add($userStar);
            $userStar->setAnime($this);
        }

        return $this;
    }

    public function removeUserStar(StarredAnime $userStar): static
    {
        if ($this->userStars->removeElement($userStar)) {
            // set the owning side to null (unless already changed)
            if ($userStar->getAnime() === $this) {
                $userStar->setAnime(null);
            }
        }

        return $this;
    }
}
