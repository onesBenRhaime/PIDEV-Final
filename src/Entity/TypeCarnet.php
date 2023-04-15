<?php

namespace App\Entity;

use App\Repository\TypeCarnetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TypeCarnetRepository::class)]
class TypeCarnet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]  
    #[ORM\Column]
    #[Groups("TypeCarnet")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups("TypeCarnet")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups("TypeCarnet")]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups("TypeCarnet")]
    private ?string $startnum = null;

    #[ORM\Column(length: 255)]
    #[Groups("TypeCarnet")]
    private ?string $endnum = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups("TypeCarnet")]
    private ?\DateTimeInterface $datecreation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups("TypeCarnet")]
    private ?\DateTimeInterface $datevalidation = null;

    #[ORM\OneToMany(mappedBy: 'idtypecarnet', targetEntity: CarnetCheque::class)]
    private Collection $carnetCheques;

    public function __construct()
    {
        $this->carnetCheques = new ArrayCollection();
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


    public function getStartnum(): ?string
    {
        return $this->startnum;
    }

    public function setStartnum(string $startnum): self
    {
        $this->startnum = $startnum;

        return $this;
    }

    public function getEndnum(): ?string
    {
        return $this->endnum;
    }

    public function setEndnum(string $endnum): self
    {
        $this->endnum = $endnum;

        return $this;
    }

    public function getDatecreation(): ?\DateTimeInterface
    {
        return $this->datecreation;
    }

    public function setDatecreation(\DateTimeInterface $datecreation): self
    {
        $this->datecreation = $datecreation;

        return $this;
    }

    public function getDatevalidation(): ?\DateTimeInterface
    {
        return $this->datevalidation;
    }

    public function setDatevalidation(\DateTimeInterface $datevalidation): self
    {
        $this->datevalidation = $datevalidation;

        return $this;
    }

    /**
     * @return Collection<int, CarnetCheque>
     */
    public function getCarnetCheques(): Collection
    {
        return $this->carnetCheques;
    }

    public function addCarnetCheque(CarnetCheque $carnetCheque): self
    {
        if (!$this->carnetCheques->contains($carnetCheque)) {
            $this->carnetCheques->add($carnetCheque);
            $carnetCheque->setIdtypecarnet($this);
        }

        return $this;
    }

    public function removeCarnetCheque(CarnetCheque $carnetCheque): self
    {
        if ($this->carnetCheques->removeElement($carnetCheque)) {
            // set the owning side to null (unless already changed)
            if ($carnetCheque->getIdtypecarnet() === $this) {
                $carnetCheque->setIdtypecarnet(null);
            }
        }

        return $this;
    }
    public function __toString(): string {
        return $this->nom;
    }
}
