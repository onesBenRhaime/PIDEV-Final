<?php

namespace App\Entity;

use App\Repository\AgenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Controller\Serializer;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AgenceRepository::class)]
class Agence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups("User")]
    private ?string $Name = null;

    #[ORM\Column(length: 255)]
    #[Groups("User")]
    private ?string $photo = null;

    #[ORM\Column(length: 255)]
    private ?string $Description = null;

    #[ORM\OneToMany(mappedBy: 'AgencyName', targetEntity: User::class)]
    private Collection $ClientId;



    public function __construct()
    {
        $this->ClientId = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getClientId(): Collection
    {
        return $this->ClientId;
    }

    public function addClientId(User $clientId): self
    {
        if (!$this->ClientId->contains($clientId)) {
            $this->ClientId->add($clientId);
            $clientId->setAgencyName($this);
        }

        return $this;
    }

    public function removeClientId(User $clientId): self
    {
        if ($this->ClientId->removeElement($clientId)) {
            // set the owning side to null (unless already changed)
            if ($clientId->getAgencyName() === $this) {
                $clientId->setAgencyName(null);
            }
        }

        return $this;
    }
    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }
}
