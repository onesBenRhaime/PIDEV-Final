<?php

namespace App\Entity;

use App\Repository\TypeCompteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: TypeCompteRepository::class)]
class TypeCompte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("types")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups("types")]
    #[Assert\NotBlank(message:"ce champs est obligatoire !")]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    #[Groups("types")]
    #[Assert\NotBlank(message:"ce champs est obligatoire !")]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'idType', targetEntity: Compte::class)]
    private Collection $comptes;

    public function __construct()
    {
        $this->comptes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Compte>
     */
    public function getComptes(): Collection
    {
        return $this->comptes;
    }

    public function addCompte(Compte $compte): self
    {
        if (!$this->comptes->contains($compte)) {
            $this->comptes->add($compte);
            $compte->setIdType($this);
        }

        return $this;
    }

    public function removeCompte(Compte $compte): self
    {
        if ($this->comptes->removeElement($compte)) {
            // set the owning side to null (unless already changed)
            if ($compte->getIdType() === $this) {
                $compte->setIdType(null);
            }
        }

        return $this;
    }
    public function __toString(): string {
        return $this->type;
    }
}
