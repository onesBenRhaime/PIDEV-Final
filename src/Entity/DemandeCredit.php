<?php

namespace App\Entity;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\DemandeCreditRepository;
use Doctrine\ORM\Mapping as ORM;
use Monolog\DateTimeImmutable;

#[ORM\Entity(repositoryClass: DemandeCreditRepository::class)]
class DemandeCredit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("demandes")]

    private ?int $id = null;

    #[ORM\Column]
    #[Groups("demandes")]

    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255)]
    #[Groups("demandes")]

    private ?string $cin1 = null;

    #[ORM\Column(length: 255)]
    #[Groups("demandes")]

    private ?string $cin2 = null;

    #[ORM\ManyToOne(inversedBy: 'demandeCredits')]
    #[Groups("demandes")]

    private ?Credit $creditId = null;

    #[ORM\ManyToOne(inversedBy: 'demandeCredits')]
    #[Groups("demandes")]

    private ?User $userId = null;

    #[ORM\Column]
    #[Groups("demandes")]

    private ?int $amount = null;

    #[ORM\Column(length: 255)]
    #[Groups("demandes")]

    private ?string $note = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    public function __construct() {
        
        $dateString = '2022-02-28 12:34:56'; 
        $dateTime = new DateTimeImmutable($dateString);
        $monologDateTime = new DateTimeImmutable($dateTime->format('Y-m-d\TH:i:s.uP'));
        $this->createdAt = $monologDateTime;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCin1(): ?string
    {
        return $this->cin1;
    }

    public function setCin1(string $cin1): self
    {
        $this->cin1 = $cin1;

        return $this;
    }

    public function getCin2(): ?string
    {
        return $this->cin2;
    }

    public function setCin2(string $cin2): self
    {
        $this->cin2 = $cin2;

        return $this;
    }

    public function getCreditId(): ?credit
    {
        return $this->creditId;
    }

    public function setCreditId(?credit $creditId): self
    {
        $this->creditId = $creditId;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(?User $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }
    public function __toString(): string {    
        return $this->creditId;
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
