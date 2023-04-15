<?php

namespace App\Entity;

use App\Repository\InvestissementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvestissementRepository::class)]
class Investissement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $min_budget = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMinBudget(): ?int
    {
        return $this->min_budget;
    }

    public function setMinBudget(int $min_budget): self
    {
        $this->min_budget = $min_budget;

        return $this;
    }
}
