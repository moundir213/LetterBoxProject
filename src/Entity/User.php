<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var Collection<int, Anime>
     */
    #[ORM\ManyToMany(targetEntity: Anime::class, inversedBy: 'usersLiking')]
    #[ORM\JoinTable(name: 'users_liked_animes')]
    private Collection $likedAnimes;

    /**
     * @var Collection<int, StarredAnime>
     */
    #[ORM\OneToMany(targetEntity: StarredAnime::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $starredAnimes;

    #[ORM\Column]
    private bool $isVerified = false;

    public function __construct()
    {
        $this->likedAnimes = new ArrayCollection();
        $this->starredAnimes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Anime>
     */
    public function getLikedAnimes(): Collection
    {
        return $this->likedAnimes;
    }

    public function addLikedAnime(Anime $likedAnime): static
    {
        if (!$this->likedAnimes->contains($likedAnime)) {
            $this->likedAnimes->add($likedAnime);
        }

        return $this;
    }

    public function removeLikedAnime(Anime $likedAnime): static
    {
        $this->likedAnimes->removeElement($likedAnime);

        return $this;
    }

    /**
     * @return Collection<int, StarredAnime>
     */
    public function getStarredAnimes(): Collection
    {
        return $this->starredAnimes;
    }

    public function getStarredAnime(Anime $anime): StarredAnime|null {
        foreach ($this->starredAnimes as $starredAnime) {
            if ($starredAnime->getAnime() === $anime) {
                return $starredAnime;
            }
        }
        return null;
    }

    public function addStarredAnime(StarredAnime $starredAnime): static
    {
        if (!$this->starredAnimes->contains($starredAnime)) {
            $this->starredAnimes->add($starredAnime);
            $starredAnime->setUser($this);
        }

        return $this;
    }

    public function removeStarredAnime(StarredAnime $starredAnime): static
    {
        if ($this->starredAnimes->removeElement($starredAnime)) {
            // set the owning side to null (unless already changed)
            if ($starredAnime->getUser() === $this) {
                $starredAnime->setUser(null);
            }
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
