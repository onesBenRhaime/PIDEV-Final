<?php

namespace App\Entity;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\CreditRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: CreditRepository::class)]
class Credit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("credits")]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'credits')]
    #[Groups("credits")]
    private ?CategoryCredit $creditCategory = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"maxAmount is required")]
    #[Groups("credits")]
    private ?int $maxAmount = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"minAmount is required")]
    #[Groups("credits")]
    private ?int $minAmount = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"withdraw is required")]
    #[Groups("credits")]
    private ?int $withdrawMonthly = null;

   

    #[ORM\Column]
    #[Assert\NotBlank(message:"months is required")]
    #[Groups("credits")]
    private ?int $months = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"loan rate is required")]
    #[Groups("credits")]

    private ?int $loanRate = null;

    #[ORM\OneToMany(mappedBy: 'creditId', targetEntity: DemandeCredit::class)]
    private Collection $demandeCredits;

    public function __construct()
    {
        $this->demandeCredits = new ArrayCollection();
    }

    

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreditCategory(): ?CategoryCredit
    {
        return $this->creditCategory;
    }

    public function setCreditCategory(?CategoryCredit $creditCategory): self
    {
        $this->creditCategory = $creditCategory;

        return $this;
    }

    public function getMaxAmount(): ?int
    {
        return $this->maxAmount;
    }

    public function setMaxAmount(int $maxAmount): self
    {
        $this->maxAmount = $maxAmount;

        return $this;
    }

    public function getMinAmount(): ?int
    {
        return $this->minAmount;
    }

    public function setMinAmount(int $minAmount): self
    {
        $this->minAmount = $minAmount;

        return $this;
    }

    public function getWithdrawMonthly(): ?int
    {
        return $this->withdrawMonthly;
    }

    public function setWithdrawMonthly(int $withdrawMonthly): self
    {
        $this->withdrawMonthly = $withdrawMonthly;

        return $this;
    }

    public function getMonths(): ?int
    {
        return $this->months;
    }

    public function setMonths(int $months): self
    {
        $this->months = $months;

        return $this;
    }

    public function getLoanRate(): ?int
    {
        return $this->loanRate;
    }

    public function setLoanRate(int $loanRate): self
    {
        $this->loanRate = $loanRate;

        return $this;
    }

    /**
     * @return Collection<int, DemandeCredit>
     */
    public function getDemandeCredits(): Collection
    {
        return $this->demandeCredits;
    }

    public function addDemandeCredit(DemandeCredit $demandeCredit): self
    {
        if (!$this->demandeCredits->contains($demandeCredit)) {
            $this->demandeCredits->add($demandeCredit);
            $demandeCredit->setCreditId($this);
        }

        return $this;
    }

    public function removeDemandeCredit(DemandeCredit $demandeCredit): self
    {
        if ($this->demandeCredits->removeElement($demandeCredit)) {
            // set the owning side to null (unless already changed)
            if ($demandeCredit->getCreditId() === $this) {
                $demandeCredit->setCreditId(null);
            }
        }

        return $this;
    }

    public function __toString(): string {    
        return $this->creditCategory;
    }
   
}
