<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FilamentRepository")
 */
class Filament
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * Unit: g
     *
     * @ORM\Column(type="decimal", precision=10, scale=0)
     * @Assert\NotBlank()
     * @Assert\PositiveOrZero()
     */
    private $weight;

    /**
     * Unit: g
     *
     * @ORM\Column(type="decimal", precision=10, scale=0)
     * @Assert\NotBlank()
     * @Assert\PositiveOrZero()
     */
    private $weightUsed = 0;

    /**
     * Unit: €
     *
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\NotBlank()
     * @Assert\PositiveOrZero()
     */
    private $price;

    /**
     * Unit: g/cm³
     *
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\NotBlank()
     * @Assert\PositiveOrZero()
     */
    private $density;

    /**
     * Unit: mm
     *
     * @ORM\Column(type="decimal", precision=5, scale=2)
     * @Assert\NotBlank()
     * @Assert\PositiveOrZero()
     */
    private $diameter;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="filaments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PrintObject", mappedBy="filament")
     */
    private $printObjects;

    public function __construct()
    {
        $this->printObjects = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->name;
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

    public function computeCostFromWeight(float $weight): ?float
    {
        if (!$this->weight || !$this->price) {
            return null;
        }

        return $this->price * $weight / $this->weight;
    }

    public function computeUsagePercentage(): int
    {
        $usedWeight = $this->weightUsed;

        foreach ($this->getPrintObjects() as $printObject) {
            if (!$printObject->getWeight()) {
                continue;
            }

            $usedWeight += $printObject->getWeight() * $printObject->getQuantity();
        }

        return $usedWeight * 100 / $this->weight;
    }
}
