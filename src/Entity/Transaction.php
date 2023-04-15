<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("transactions")]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'compteTransaction')]
    
    #[Assert\NotBlank(message:"This field is mandatory!")]
    private ?Compte $compte = null;

    #[ORM\Column(length: 255)]
    #[Groups("transactions")]
    #[Assert\NotBlank(message:"This field is mandatory!")]
    private ?string $typeTransaction = null;

    #[ORM\Column(length: 255)]
    #[Groups("transactions")]    
    #[Assert\NotBlank(message:"This field is mandatory!")]
    private ?string $montant = null;


    #[ORM\Column(length: 255)]
    #[Groups("transactions")]
    #[Assert\NotBlank(message:"This field is mandatory! and The Accout Request must 14 number!")]
    private ?string $requestFrom = null;

    #[ORM\Column(length: 255)]
    #[Groups("transactions")]
    #[Assert\NotBlank(message:"This field is mandatory! and The Accout Request must 14 number! ")]
    private ?string $requestTo = null;

    #[ORM\Column(length: 255)]
    #[Groups("transactions")]
    #[Assert\NotBlank(message:"This field is mandatory!")]
    private ?string $statue = null;

    #[ORM\Column(length: 255)]
    #[Groups("transactions")]
    #[Assert\NotBlank(message:"This name of the agance is mandatory!")]
    private ?string $agenceName = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups("transactions")]
    private ?\DateTimeInterface $date = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompte(): ?Compte
    {
        return $this->compte;
    }

    public function setCompte(?Compte $compte): self
    {
        $this->compte = $compte;

        return $this;
    }

    public function getTypeTransaction(): ?string
    {
        return $this->typeTransaction;
    }

    public function setTypeTransaction(string $typeTransaction): self
    {
        $this->typeTransaction = $typeTransaction;

        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getRequestFrom(): ?string
    {
        return $this->requestFrom;
    }

    public function setRequestFrom(string $requestFrom): self
    {
        $this->requestFrom = $requestFrom;

        return $this;
    }

    public function getRequestTo(): ?string
    {
        return $this->requestTo;
    }

    public function setRequestTo(string $requestTo): self
    {
        $this->requestTo = $requestTo;

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

    public function getAgenceName(): ?string
    {
        return $this->agenceName;
    }

    public function setAgenceName(string $agenceName): self
    {
        $this->agenceName = $agenceName;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

}
