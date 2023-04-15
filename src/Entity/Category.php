<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("categories")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Name is required")]
    #[Groups("categories")]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Blog::class)]
    private Collection $BlogID;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Decsription is required")]
    #[Groups("categories")]
    private ?string $description = null;

    public function __construct()
    {
        $this->BlogID = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Blog>
     */
    public function getBlogID(): Collection
    {
        return $this->BlogID;
    }

    public function addBlogID(Blog $blogID): self
    {
        if (!$this->BlogID->contains($blogID)) {
            $this->BlogID->add($blogID);
            $blogID->setCategory($this);
        }

        return $this;
    }

    public function removeBlogID(Blog $blogID): self
    {
        if ($this->BlogID->removeElement($blogID)) {
            // set the owning side to null (unless already changed)
            if ($blogID->getCategory() === $this) {
                $blogID->setCategory(null);
            }
        }

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
}
