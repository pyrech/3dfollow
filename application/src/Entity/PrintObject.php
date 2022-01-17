<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use App\Repository\PrintObjectRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @Vich\Uploadable
 */
#[ORM\Entity(repositoryClass: PrintObjectRepository::class)]
class PrintObject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'guid')]
    private string $uuid;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: Filament::class, inversedBy: 'printObjects')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?Filament $filament = null;

    #[ORM\Embedded(class: EmbeddedFile::class)]
    private ?EmbeddedFile $gCode = null;

    /**
     * @Vich\UploadableField(mapping="print_oject", fileNameProperty="gCode.name", size="gCode.size", mimeType="gCode.mimeType", originalName="gCode.originalName", dimensions="gCode.dimensions")
     */
    #[Assert\File(maxSize: '128M')]
    #[Assert\PositiveOrZero]
    private ?File $gCodeFile = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(value: 1)]
    private ?int $quantity = 1;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    #[Assert\NotBlank(message: 'validation.value_required_no_gcode', groups: ['no_gcode_uploaded'])]
    #[Assert\PositiveOrZero]
    private ?string $weight = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    #[Assert\NotBlank(message: 'validation.value_required_no_gcode', groups: ['no_gcode_uploaded'])]
    #[Assert\PositiveOrZero]
    private ?string $length = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    #[Assert\NotBlank(message: 'validation.value_required_no_gcode', groups: ['no_gcode_uploaded'])]
    #[Assert\PositiveOrZero]
    private ?string $cost = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'printObjects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: PrintRequest::class, inversedBy: 'printObjects')]
    private ?PrintRequest $printRequest = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $printedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->uuid = uuid_create();
        $this->gCode = new EmbeddedFile();
        $this->printedAt = new \DateTime();
    }

    public function __toString()
    {
        return $this->name ?: 'New print';
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
    public function setGCodeFile(?File $gCodeFile = null): void
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

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(?string $weight): self
    {
        $this->weight = $weight;

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
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
