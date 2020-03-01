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
     * @ORM\OneToMany(targetEntity="PrintRequest", mappedBy="team")
     */
    private $printRequests;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $joinToken;

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
        $this->printRequests = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;

        if ($creator->getTeamCreated() !== $this) {
            $creator->setTeamCreated($this);
        }

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
            $member->addTeam($this);
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
            $printRequest->setTeam($this);
        }

        return $this;
    }

    public function removePrintRequest(PrintRequest $printRequest): self
    {
        if ($this->printRequests->contains($printRequest)) {
            $this->printRequests->removeElement($printRequest);
            // set the owning side to null (unless already changed)
            if ($printRequest->getTeam() === $this) {
                $printRequest->setTeam(null);
            }
        }

        return $this;
    }

    public function getJoinToken(): ?string
    {
        return $this->joinToken;
    }

    public function setJoinToken(?string $joinToken): self
    {
        $this->joinToken = $joinToken;

        return $this;
    }
}
