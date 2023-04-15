<?php

namespace App\Entity;

use App\Repository\OffreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OffreRepository::class)]
class Offre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Embauche $offreEmbauche = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Investissement $offreInvestissement = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Assurance $offreAssurance = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOffreEmbauche(): ?Embauche
    {
        return $this->offreEmbauche;
    }

    public function setOffreEmbauche(?Embauche $offreEmbauche): self
    {
        $this->offreEmbauche = $offreEmbauche;

        return $this;
    }

    public function getOffreInvestissement(): ?Investissement
    {
        return $this->offreInvestissement;
    }

    public function setOffreInvestissement(?Investissement $offreInvestissement): self
    {
        $this->offreInvestissement = $offreInvestissement;

        return $this;
    }

    public function getOffreAssurance(): ?Assurance
    {
        return $this->offreAssurance;
    }

    public function setOffreAssurance(?Assurance $offreAssurance): self
    {
        $this->offreAssurance = $offreAssurance;

        return $this;
    }
}
