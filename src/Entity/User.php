<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
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
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PrintItem", mappedBy="user", orphanRemoval=true)
     */
    private $printItems;

    public function __construct()
    {
        $this->printItems = new ArrayCollection();
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
     * @return Collection|PrintItem[]
     */
    public function getPrintItems(): Collection
    {
        return $this->printItems;
    }

    public function addPrintItem(PrintItem $printItem): self
    {
        if (!$this->printItems->contains($printItem)) {
            $this->printItems[] = $printItem;
            $printItem->setUser($this);
        }

        return $this;
    }

    public function removePrintItem(PrintItem $printItem): self
    {
        if ($this->printItems->contains($printItem)) {
            $this->printItems->removeElement($printItem);
            // set the owning side to null (unless already changed)
            if ($printItem->getUser() === $this) {
                $printItem->setUser(null);
            }
        }

        return $this;
    }
}
