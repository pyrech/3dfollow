<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PrintObjectRepository")
 * @Vich\Uploadable
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
     * @ORM\Column(type="guid")
     */
    private $uuid;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Filament", inversedBy="printObjects")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $filament;

    /**
     * @ORM\Embedded(class="Vich\UploaderBundle\Entity\File")
     *
     * @var EmbeddedFile
     */
    private $gCode;

    /**
     * @Vich\UploadableField(mapping="print_oject", fileNameProperty="gCode.name", size="gCode.size", mimeType="gCode.mimeType", originalName="gCode.originalName", dimensions="gCode.dimensions")
     * @Assert\File(maxSize="50M")
     * @Assert\PositiveOrZero()
     *
     * @var File|null
     */
    private $gCodeFile;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(value=1)
     */
    private $quantity = 1;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     * @Assert\PositiveOrZero()
     */
    private $length;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     * @Assert\PositiveOrZero()
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

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTimeInterface|null
     */
    private $updatedAt;

    public function __construct()
    {
        $this->uuid = uuid_create();
        $this->gCode = new EmbeddedFile();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
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

    public function getGCode(): ?EmbeddedFile
    {
        return $this->gCode;
    }

    public function setGCode(?EmbeddedFile $gCode): self
    {
        $this->gCode = $gCode;

        return $this;
    }

    /**
     * @param File|UploadedFile|null $gCodeFile
     */
    public function setGCodeFile(?File $gCodeFile = null)
    {
        $this->gCodeFile = $gCodeFile;

        if (null !== $gCodeFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getGCodeFile(): ?File
    {
        return $this->gCodeFile;
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

    public function getUploadDirectory(): string
    {
        $directory = '';

        if ($this->user) {
            $directory .= $this->user->getId() . '/';
        }

        $directory .= $this->uuid;

        return $directory;
    }
}
