<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PrintObjectRepository")
 */
class PrintObject
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Filament", inversedBy="printObjects")
     * @ORM\JoinColumn(nullable=false)
     */
    private $filament;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fileName;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(value=1)
     */
    private $quantity = 1;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $length;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $cost;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="printObjects")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PrintRequest", inversedBy="printObjects")
     */
    private $printRequest;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $printedAt;

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

    public function getFilament(): ?Filament
    {
        return $this->filament;
    }

    public function setFilament(?Filament $filament): self
    {
        $this->filament = $filament;

        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): self
    {
        $this->fileName = $fileName;

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

    public function getLength(): ?string
    {
        return $this->length;
    }

    public function setLength(?string $length): self
    {
        $this->length = $length;

        return $this;
    }

    public function getCost(): ?string
    {
        return $this->cost;
    }

    public function setCost(?string $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPrintRequest(): ?PrintRequest
    {
        return $this->printRequest;
    }

    public function setPrintRequest(?PrintRequest $printRequest): self
    {
        $this->printRequest = $printRequest;

        return $this;
    }

    public function getPrintedAt(): ?\DateTimeInterface
    {
        return $this->printedAt;
    }

    public function setPrintedAt(?\DateTimeInterface $printedAt): self
    {
        $this->printedAt = $printedAt;

        return $this;
    }
}
