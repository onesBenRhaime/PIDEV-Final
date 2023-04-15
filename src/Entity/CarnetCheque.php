<?php

namespace App\Entity;

use App\Repository\CarnetChequeRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: CarnetChequeRepository::class)]
class CarnetCheque
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
 
    #[ORM\Column]
    #[Groups("CarnetCheque")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups("CarnetCheque")]
    #[Assert\NotBlank(message:"This field is required !")]
    #[Assert\Email(message:"The email '{{ value }}' is not a valid email ")]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups("CarnetCheque")]
    private ?string $identifier = null;

    #[ORM\Column(length: 255)]
    #[Groups("CarnetCheque")]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups("CarnetCheque")]
    private ?string $cinS1 = null;

    #[ORM\Column(length: 255)]
    #[Groups("CarnetCheque")]
    private ?string $cinS2 = null;

    #[ORM\Column(length: 255)]
    #[Groups("CarnetCheque")]
    private ?string $document = null;

    #[ORM\ManyToOne(inversedBy: 'carnetCheques')]
    private ?TypeCarnet $idtypecarnet = null;

    #[ORM\Column(length: 255)]
    #[Groups("CarnetCheque")]
    private ?string $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

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

    public function getDocument(): ?string
    {
        return $this->document;
    }

    public function setDocument(string $document): self
    {
        $this->document = $document;

        return $this;
    }

    public function getIdtypecarnet(): ?TypeCarnet
    {
        return $this->idtypecarnet;
    }

    public function setIdtypecarnet(?TypeCarnet $idtypecarnet): self
    {
        $this->idtypecarnet = $idtypecarnet;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    
}
