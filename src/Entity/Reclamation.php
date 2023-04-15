<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\serializer\Normalizer\NormalizerInterface;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("reclamations")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Title is required")]
    #[Groups("reclamations")]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Description is required")]
    #[Groups("reclamations")]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Required")]
    #[Groups("reclamations")]
    private ?string $Priorite = null;

    #[ORM\ManyToOne(targetEntity: TypeReclamation::class, inversedBy: 'reclamations')]
    private ?TypeReclamation $TypeReclamation = null;

    #[ORM\ManyToOne(targetEntity: User::class,inversedBy: 'reclamations')]
    private ?user $ClientName = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getPriorite(): ?string
    {
        return $this->Priorite;
    }

    public function setPriorite(string $Priorite): self
    {
        $this->Priorite = $Priorite;

        return $this;
    }

    public function getTypeReclamation(): ?TypeReclamation
    {
        return $this->TypeReclamation;
    }

    public function setTypeReclamation(?TypeReclamation $TypeReclamation): self
    {
        $this->TypeReclamation = $TypeReclamation;

        return $this;
    }

    public function getClientName(): ?user
    {
        return $this->ClientName;
    }

    public function setClientName(?user $ClientName): self
    {
        $this->ClientName = $ClientName;

        return $this;
    }
}
