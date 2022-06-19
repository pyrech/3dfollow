<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use App\Repository\FilamentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FilamentRepository::class)]
class Filament
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    /**
     * Weight in grams (g).
     */
    #[ORM\Column(type: 'decimal', precision: 10, scale: 0)]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    private ?string $weight = null;

    /**
     * Quantity of filament used in grams (g).
     */
    #[ORM\Column(type: 'decimal', precision: 10, scale: 0)]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    private ?string $weightUsed = '0';

    /**
     * Price in euro (€).
     */
    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    private ?string $price = null;

    /**
     * Density in g/cm³.
     */
    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    private ?string $density = null;

    /**
     * Diameter in millimeters (mm).
     */
    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    private ?string $diameter = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'filaments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\OneToMany(targetEntity: PrintObject::class, mappedBy: 'filament')]
    private Collection $printObjects;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->printObjects = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function __toString()
    {
        return $this->name ? ($this->name . ' (' . floor($this->computeUsagePercentage()) . ' %)') : 'New filament';
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

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(?string $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getWeightUsed(): ?string
    {
        return $this->weightUsed;
    }

    public function setWeightUsed(?string $weightUsed): self
    {
        $this->weightUsed = $weightUsed;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDensity(): ?string
    {
        return $this->density;
    }

    public function setDensity(?string $density): self
    {
        $this->density = $density;

        return $this;
    }

    public function getDiameter(): ?string
    {
        return $this->diameter;
    }

    public function setDiameter(?string $diameter): self
    {
        $this->diameter = $diameter;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

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
            $printObject->setFilament($this);
        }

        return $this;
    }

    public function removePrintObject(PrintObject $printObject): self
    {
        if ($this->printObjects->contains($printObject)) {
            $this->printObjects->removeElement($printObject);
            // set the owning side to null (unless already changed)
            if ($printObject->getFilament() === $this) {
                $printObject->setFilament(null);
            }
        }

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function computeCostFromWeight(float $weight): ?float
    {
        if (!$this->weight || !$this->price) {
            return null;
        }

        return ((float) $this->price) * $weight / (float) $this->weight;
    }

    public function computeUsagePercentage(): float
    {
        $usedWeight = (float) $this->weightUsed;

        foreach ($this->getPrintObjects() as $printObject) {
            if (!$printObject->getWeight()) {
                continue;
            }

            $usedWeight += ((float) $printObject->getWeight()) * $printObject->getQuantity();
        }

        return $usedWeight * 100 / (float) $this->weight;
    }
}
