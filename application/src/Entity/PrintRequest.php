<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use App\Repository\PrintRequestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PrintRequestRepository::class)]
class PrintRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $link = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isPrinted = false;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(value: 1)]
    private ?int $quantity = 1;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'printRequests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'printRequests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Team $team = null;

    #[ORM\OneToMany(targetEntity: PrintObject::class, mappedBy: 'printRequest')]
    private Collection $printObjects;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->printObjects = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function __toString()
    {
        return $this->name ?: 'New print request';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getIsPrinted(): ?bool
    {
        return $this->isPrinted;
    }

    public function setIsPrinted(bool $isPrinted): self
    {
        $this->isPrinted = $isPrinted;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

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
            $printObject->setPrintRequest($this);
        }

        return $this;
    }

    public function removePrintObject(PrintObject $printObject): self
    {
        if ($this->printObjects->contains($printObject)) {
            $this->printObjects->removeElement($printObject);
            // set the owning side to null (unless already changed)
            if ($printObject->getPrintRequest() === $this) {
                $printObject->setPrintRequest(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getTotalCost(): float
    {
        $unitCost = 0;

        foreach ($this->getPrintObjects() as $printObject) {
            if ($printObject->getCost()) {
                $unitCost += ((float) $printObject->getCost()) * $printObject->getQuantity();
            }
        }

        return $unitCost * $this->quantity;
    }
}
