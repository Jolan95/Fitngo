<?php

namespace App\Entity;

use App\Repository\FranchiseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FranchiseRepository::class)]
class Franchise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\OneToMany(mappedBy: 'Franchise', targetEntity: Structure::class, orphanRemoval: true)]
    private Collection $structures;

    #[ORM\OneToOne(mappedBy: 'Franchise', cascade: ['persist', 'remove'])]
    private ?User $user_info = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Permit $Permit = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $last_connection = null;

    public function __construct()
    {
        $this->structures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection<int, Structure>
     */
    public function getStructures(): Collection
    {
        return $this->structures;
    }

    public function addStructure(Structure $structure): self
    {
        if (!$this->structures->contains($structure)) {
            $this->structures[] = $structure;
            $structure->setFranchise($this);
        }

        return $this;
    }

    public function removeStructure(Structure $structure): self
    {
        if ($this->structures->removeElement($structure)) {
            // set the owning side to null (unless already changed)
            if ($structure->getFranchise() === $this) {
                $structure->setFranchise(null);
            }
        }

        return $this;
    }

    public function getUserInfo(): ?User
    {
        return $this->user_info;
    }

    public function setUserInfo(?User $user_info): self
    {
        // unset the owning side of the relation if necessary
        if ($user_info === null && $this->user_info !== null) {
            $this->user_info->setFranchise(null);
        }

        // set the owning side of the relation if necessary
        if ($user_info !== null && $user_info->getFranchise() !== $this) {
            $user_info->setFranchise($this);
        }

        $this->user_info = $user_info;

        return $this;
    }

    public function getPermit(): ?Permit
    {
        return $this->Permit;
    }

    public function setPermit(Permit $Permit): self
    {
        $this->Permit = $Permit;

        return $this;
    }

    public function getLastConnection(): ?\DateTimeInterface
    {
        return $this->last_connection;
    }

    public function setLastConnection(\DateTimeInterface $last_connection): self
    {
        $this->last_connection = $last_connection;

        return $this;
    }
}
