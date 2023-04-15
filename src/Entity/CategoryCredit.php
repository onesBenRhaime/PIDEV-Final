<?php

namespace App\Entity;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\CategoryCreditRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryCreditRepository::class)]
class CategoryCredit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("credit_categories")]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'creditCategory', targetEntity: Credit::class)]
    private Collection $credits;

    #[ORM\Column(length: 255)]
    #[Groups("credit_categories")]
    #[Assert\NotBlank(message:"name is required")]
    #[Assert\Length(
        min: 3,
        max: 20,
        minMessage: "Field 'name' must be at least {{ limit }} characters long",
        maxMessage: "Field 'name' cannot be longer than {{ limit }} characters"
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups("credit_categories")]
    #[Assert\NotBlank(message:"description is required")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Field 'description' must be at least {{ limit }} characters long",
        maxMessage: "Field 'description' cannot be longer than {{ limit }} characters"
    )]
    private ?string $description = null;

    public function __construct()
    {
        $this->credits = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getCredit(): ?Credit
    {
        return $this->credit;
    }
    public function setCredit(?Credit $credit): self
    {
        $this->credit = $credit;

        return $this;
    }

    /**
     * @return Collection<int, Credit>
     */
    public function getCredits(): Collection
    {
        return $this->credits;
    }

    public function addCredit(Credit $credit): self
    {
        if (!$this->credits->contains($credit)) {
            $this->credits->add($credit);
            $credit->setCreditCategory($this);
        }

        return $this;
    }

    public function removeCredit(Credit $credit): self
    {
        if ($this->credits->removeElement($credit)) {
            // set the owning side to null (unless already changed)
            if ($credit->getCreditCategory() === $this) {
                $credit->setCreditCategory(null);
            }
        }

        return $this;
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

    public function __toString(): string {    
        return $this->name;
    }
}
