<?php

namespace App\Entity;

use App\Repository\TypeCarteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeCarteRepository::class)]
class TypeCarte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("typeCartes")]
    private ?int $id = null;

    #[ORM\Column(length: 255)] 
    #[Groups("typeCartes")]   
    #[Assert\NotBlank(message:"ce champs est obligatoire !")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups("typeCartes")]
    #[Assert\NotBlank(message:"ce champs est obligatoire !")]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'idtypecarte', targetEntity: CarteBancaire::class)]
    private Collection $carteBancaires;

    public function __construct()
    {
        $this->carteBancaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

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
     * @return Collection<int, CarteBancaire>
     */
    public function getCarteBancaires(): Collection
    {
        return $this->carteBancaires;
    }

    public function addCarteBancaire(CarteBancaire $carteBancaire): self
    {
        if (!$this->carteBancaires->contains($carteBancaire)) {
            $this->carteBancaires->add($carteBancaire);
            $carteBancaire->setIdtypecarte($this);
        }

        return $this;
    }

    public function removeCarteBancaire(CarteBancaire $carteBancaire): self
    {
        if ($this->carteBancaires->removeElement($carteBancaire)) {
            // set the owning side to null (unless already changed)
            if ($carteBancaire->getIdtypecarte() === $this) {
                $carteBancaire->setIdtypecarte(null);
            }
        }

        return $this;
    }

    public function __toString(): string {
        return $this->nom;
    }
}
