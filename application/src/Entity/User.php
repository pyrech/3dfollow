<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['username'], message: 'validation.username_existing')]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\NotBlank(message: 'validation.username_required')]
    private ?string $username = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isAdmin = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isPrinter = false;

    /**
     * @var string The hashed password
     */
    #[ORM\Column(type: 'string')]
    private ?string $password = null;

    #[ORM\OneToMany(targetEntity: PrintRequest::class, mappedBy: 'user')]
    private Collection $printRequests;

    #[ORM\OneToMany(targetEntity: Filament::class, mappedBy: 'owner', orphanRemoval: true)]
    private Collection $filaments;

    #[ORM\OneToOne(targetEntity: Team::class, mappedBy: 'creator', cascade: ['persist', 'remove'])]
    private ?Team $teamCreated = null;

    #[ORM\ManyToMany(targetEntity: Team::class, mappedBy: 'members')]
    private Collection $teams;

    #[ORM\OneToMany(targetEntity: PrintObject::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $printObjects;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $lastChangelogSeenAt = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $defaultLocale = null;

    public function __construct()
    {
        $this->printRequests = new ArrayCollection();
        $this->filaments = new ArrayCollection();
        $this->teams = new ArrayCollection();
        $this->printObjects = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function __toString()
    {
        return $this->username ?: 'New user';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getIsAdmin(): bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    public function getIsPrinter(): bool
    {
        return $this->isPrinter;
    }

    public function setIsPrinter(bool $isPrinter): self
    {
        $this->isPrinter = $isPrinter;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        // guarantee every user at least has ROLE_USER
        $roles = ['ROLE_USER'];

        if ($this->isAdmin) {
            $roles[] = 'ROLE_ADMIN';
        }

        if ($this->isPrinter) {
            $roles[] = 'ROLE_PRINTER';
        }

        if (\count($this->teams) > 0) {
            $roles[] = 'ROLE_TEAM_MEMBER';
        }

        return array_unique($roles);
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @return Collection<PrintRequest>
     */
    public function getPrintRequests(): Collection
    {
        return $this->printRequests;
    }

    public function addPrintRequest(PrintRequest $printRequest): self
    {
        if (!$this->printRequests->contains($printRequest)) {
            $this->printRequests[] = $printRequest;
            $printRequest->setUser($this);
        }

        return $this;
    }

    public function removePrintRequest(PrintRequest $printRequest): self
    {
        if ($this->printRequests->contains($printRequest)) {
            $this->printRequests->removeElement($printRequest);
            // set the owning side to null (unless already changed)
            if ($printRequest->getUser() === $this) {
                $printRequest->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<Filament>
     */
    public function getFilaments(): Collection
    {
        return $this->filaments;
    }

    public function addFilament(Filament $filament): self
    {
        if (!$this->filaments->contains($filament)) {
            $this->filaments[] = $filament;
            $filament->setOwner($this);
        }

        return $this;
    }

    public function removeFilament(Filament $filament): self
    {
        if ($this->filaments->contains($filament)) {
            $this->filaments->removeElement($filament);
            // set the owning side to null (unless already changed)
            if ($filament->getOwner() === $this) {
                $filament->setOwner(null);
            }
        }

        return $this;
    }

    public function getTeamCreated(): ?Team
    {
        return $this->teamCreated;
    }

    public function setTeamCreated(Team $teamCreated): self
    {
        $this->teamCreated = $teamCreated;

        // set the owning side of the relation if necessary
        if ($teamCreated->getCreator() !== $this) {
            $teamCreated->setCreator($this);
        }

        return $this;
    }

    /**
     * @return Collection<Team>
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
            $team->addMember($this);
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        if ($this->teams->contains($team)) {
            $this->teams->removeElement($team);
            $team->removeMember($this);
        }

        return $this;
    }

    /**
     * @return Collection<PrintObject>
     */
    public function getPrintObjects(): Collection
    {
        return $this->printObjects;
    }

    public function addPrintObject(PrintObject $printObject): self
    {
        if (!$this->printObjects->contains($printObject)) {
            $this->printObjects[] = $printObject;
            $printObject->setUser($this);
        }

        return $this;
    }

    public function removePrintObject(PrintObject $printObject): self
    {
        if ($this->printObjects->contains($printObject)) {
            $this->printObjects->removeElement($printObject);
        }

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getLastChangelogSeenAt(): ?\DateTimeInterface
    {
        return $this->lastChangelogSeenAt;
    }

    public function setLastChangelogSeenAt(?\DateTimeInterface $lastChangelogSeenAt): self
    {
        $this->lastChangelogSeenAt = $lastChangelogSeenAt;

        return $this;
    }

    public function getDefaultLocale(): ?string
    {
        return $this->defaultLocale;
    }

    public function setDefaultLocale(?string $defaultLocale): self
    {
        $this->defaultLocale = $defaultLocale;

        return $this;
    }
}
