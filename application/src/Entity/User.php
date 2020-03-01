<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 * @ORM\Table(name="users")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAdmin = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPrinter = false;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="PrintRequest", mappedBy="user")
     */
    private $printRequests;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Filament", mappedBy="owner", orphanRemoval=true)
     */
    private $filaments;

    /**
     * @ORM\OneToOne(targetEntity="Team", mappedBy="creator", cascade={"persist", "remove"})
     */
    private $teamCreated;

    /**
     * @ORM\ManyToMany(targetEntity="Team", mappedBy="members")
     */
    private $teams;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PrintObject", mappedBy="user", orphanRemoval=true)
     */
    private $printObjects;

    public function __construct()
    {
        $this->printRequests = new ArrayCollection();
        $this->filaments = new ArrayCollection();
        $this->teams = new ArrayCollection();
        $this->printObjects = new ArrayCollection();
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

    public function setUsername(string $username): self
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

        if (count($this->teams) > 0) {
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
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function __toString()
    {
        return $this->username;
    }

    /**
     * @return Collection|PrintRequest[]
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
     * @return Collection|Filament[]
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
     * @return Collection|Team[]
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
     * @return Collection|PrintObject[]
     */
    public function getPrintObjects(): Collection
    {
        return $this->printObjects;
    }

    public function addPrintObject(PrintObject $printObject): self
    {
        if (!$this->printObjects->contains($printObject)) {
            $this->printObjects[] = $printObject;
            $printObject->setOwner($this);
        }

        return $this;
    }

    public function removePrintObject(PrintObject $printObject): self
    {
        if ($this->printObjects->contains($printObject)) {
            $this->printObjects->removeElement($printObject);
            // set the owning side to null (unless already changed)
            if ($printObject->getOwner() === $this) {
                $printObject->setOwner(null);
            }
        }

        return $this;
    }
}
