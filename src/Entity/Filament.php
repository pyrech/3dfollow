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
     */
    private $weight;

    /**
     * Unit: €
     *
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\NotBlank()
     */
    private $price;

    /**
     * Unit: g/cm³
     *
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\NotBlank()
     */
    private $density;

    /**
     * Unit: mm
     *
     * @ORM\Column(type="decimal", precision=5, scale=2)
     * @Assert\NotBlank()
     */
    private $diameter;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="filaments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PrintItem", mappedBy="filament")
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

    public function __toString()
    {
        return $this->name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(string $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDensity(): ?string
    {
        return $this->density;
    }

    public function setDensity(string $density): self
    {
        $this->density = $density;

        return $this;
    }

    public function getDiameter(): ?string
    {
        return $this->diameter;
    }

    public function setDiameter(string $diameter): self
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
            $printItem->setFilament($this);
        }

        return $this;
    }

    public function removePrintItem(PrintItem $printItem): self
    {
        if ($this->printItems->contains($printItem)) {
            $this->printItems->removeElement($printItem);
            // set the owning side to null (unless already changed)
            if ($printItem->getFilament() === $this) {
                $printItem->setFilament(null);
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
        $usedWeight = 0;

        foreach ($this->getPrintItems() as $printItem) {
            if (!$printItem->getIsPrinted()) {
                continue;
            }

            $usedWeight += $printItem->getWeight() * $printItem->getQuantity();
        }

        return $usedWeight * 100 / $this->weight;
    }
}
