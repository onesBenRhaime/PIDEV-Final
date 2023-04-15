<?php

namespace App\Entity;

use App\Repository\CompteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CompteRepository::class)]
class Compte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'compte', targetEntity: Transaction::class)]
    private Collection $compteTransaction;


    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateFermeture = null;

    #[ORM\Column(length: 255)]    
   // #[Assert\NotBlank(message:"This field is mandatory!")]
    private ?string $solde = null;

    #[ORM\ManyToOne(inversedBy: 'comptes')]
   // #[Assert\NotBlank(message:"This field is mandatory!")]
    private ?User $idUser = null;

    #[ORM\ManyToOne(inversedBy: 'comptes')]
  //  #[Assert\NotBlank(message:"This field is mandatory!")]
    private ?TypeCompte $idType = null;

    #[ORM\Column(length: 255)]
    private ?string $cinS1 = null;

    #[ORM\Column(length: 255)]
    private ?string $cinS2 = null;

    #[ORM\Column(length: 255)] 
    private ?string $otherDoc = null;

    #[ORM\Column]
   // #[Assert\NotBlank(message:"This field is mandatory!")]
    private ?int $maxSolde = null;

    #[ORM\Column]
  //  #[Assert\NotBlank(message:"This field is mandatory!")]
    private ?int $minSolde = null;

    #[ORM\Column]
   // #[Assert\NotBlank(message:"This field is mandatory!")]
    private ?int $redSolde = null;

    #[ORM\Column(length: 255)]    
   // #[Assert\NotBlank(message:"The bank account number (RIB) is mandatory!, and must contain 14 digits.")]
    private ?string $rib = null;

    #[ORM\Column(length: 255)]
  //  #[Assert\NotBlank(message:"status is mandatory!")]
    private ?string $statue = null;

   
   

    public function __construct()
    {
        $this->compteTransaction = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getCompteTransaction(): Collection
    {
        return $this->compteTransaction;
    }

    public function addCompteTransaction(Transaction $compteTransaction): self
    {
        if (!$this->compteTransaction->contains($compteTransaction)) {
            $this->compteTransaction->add($compteTransaction);
            $compteTransaction->setCompte($this);
        }

        return $this;
    }

    public function removeCompteTransaction(Transaction $compteTransaction): self
    {
        if ($this->compteTransaction->removeElement($compteTransaction)) {
            // set the owning side to null (unless already changed)
            if ($compteTransaction->getCompte() === $this) {
                $compteTransaction->setCompte(null);
            }
        }

        return $this;
    }


    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDateFermeture(): ?\DateTimeInterface
    {
        return $this->dateFermeture;
    }

    public function setDateFermeture(\DateTimeInterface $dateFermeture): self
    {
        $this->dateFermeture = $dateFermeture;

        return $this;
    }

    public function getSolde(): ?string
    {
        return $this->solde;
    }

    public function setSolde(string $solde): self
    {
        $this->solde = $solde;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getIdType(): ?TypeCompte
    {
        return $this->idType;
    }

    public function setIdType(?TypeCompte $idType): self
    {
        $this->idType = $idType;

        return $this;
    }

    public function getCinS1(): ?string
    {
        return $this->cinS1;
    }

    public function setCinS1(string $cinS1): self
    {
        $this->cinS1 = $cinS1;

        return $this;
    }

    public function getCinS2(): ?string
    {
        return $this->cinS2;
    }

    public function setCinS2(string $cinS2): self
    {
        $this->cinS2 = $cinS2;

        return $this;
    }

    public function getOtherDoc(): ?string
    {
        return $this->otherDoc;
    }

    public function setOtherDoc(string $otherDoc): self
    {
        $this->otherDoc = $otherDoc;

        return $this;
    }

    public function getMaxSolde(): ?int
    {
        return $this->maxSolde;
    }

    public function setMaxSolde(int $maxSolde): self
    {
        $this->maxSolde = $maxSolde;

        return $this;
    }

    public function getMinSolde(): ?int
    {
        return $this->minSolde;
    }

    public function setMinSolde(int $minSolde): self
    {
        $this->minSolde = $minSolde;

        return $this;
    }

    public function getRedSolde(): ?int
    {
        return $this->redSolde;
    }

    public function setRedSolde(int $redSolde): self
    {
        $this->redSolde = $redSolde;

        return $this;
    }

    public function getRib(): ?string
    {
        return $this->rib;
    }

    public function setRib(string $rib): self
    {
        $this->rib = $rib;

        return $this;
    }

    public function getStatue(): ?string
    {
        return $this->statue;
    }

    public function setStatue(string $statue): self
    {
        $this->statue = $statue;

        return $this;
    }
    public function __toString(): string {
        return $this->rib;
    }

  
}
