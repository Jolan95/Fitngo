<?php

namespace App\Entity;

use App\Repository\StructureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StructureRepository::class)]

class Structure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $last_connection = null;

    #[ORM\ManyToOne(inversedBy: 'structures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Franchise $Franchise = null;

    #[ORM\OneToOne(mappedBy: 'Structure', cascade: ['persist', 'remove'])]
    private ?User $user_info = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Permit $Permit = null;

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

    public function getLastConnection(): ?\DateTimeInterface
    {
        return $this->last_connection;
    }

    public function setLastConnection(?\DateTimeInterface $last_connection): self
    {
        $this->last_connection = $last_connection;

        return $this;
    }

    public function getFranchise(): ?Franchise
    {
        return $this->Franchise;
    }

    public function setFranchise(?Franchise $Franchise): self
    {
        $this->Franchise = $Franchise;

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
            $this->user_info->setStructure(null);
        }

        // set the owning side of the relation if necessary
        if ($user_info !== null && $user_info->getStructure() !== $this) {
            $user_info->setStructure($this);
        }

        $this->user_info = $user_info;

        return $this;
    }

    public function getPermit(): ?Permit
    {
        return $this->Permit;
    }

    public function setPermit(?Permit $Permit): self
    {
        $this->Permit = $Permit;

        return $this;
    }
}
