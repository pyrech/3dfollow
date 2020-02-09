<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TeamRepository")
 */
class Team
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="teamCreated", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $creator;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="teams")
     */
    private $members;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PrintItem", mappedBy="team")
     */
    private $printItems;

    public function __toString()
    {
        if (!$this->creator) {
            return 'new team';
        }

        return sprintf('%s\'s team', $this->creator);
    }

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->printItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(User $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
        }

        return $this;
    }

    public function removeMember(User $member): self
    {
        if ($this->members->contains($member)) {
            $this->members->removeElement($member);
        }

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
            $printItem->setTeam($this);
        }

        return $this;
    }

    public function removePrintItem(PrintItem $printItem): self
    {
        if ($this->printItems->contains($printItem)) {
            $this->printItems->removeElement($printItem);
            // set the owning side to null (unless already changed)
            if ($printItem->getTeam() === $this) {
                $printItem->setTeam(null);
            }
        }

        return $this;
    }
}
