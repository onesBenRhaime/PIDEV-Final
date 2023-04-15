<?php

namespace App\Entity;

use App\Repository\BlogRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
#[ORM\Entity(repositoryClass: BlogRepository::class)]
class Blog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("blogs")]
    private ?int $id = null;

    
    #[ORM\Column]
    #[Groups("blogs")]
    private ?int $category_id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Name is required")]
    #[Groups("blogs")]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Description is required")]
    #[Groups("blogs")]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups("blogs")]
    private ?string $photo = null;

    #[Groups("blogs")]
    #[ORM\ManyToOne(inversedBy: 'BlogID')]
    #[ORM\JoinColumn(name:"category_id", referencedColumnName:"id")]
    
    private ?Category $category = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Details are required")]
    #[Groups("blogs")]
    private ?string $details = null;

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

    public function getCategoryId(): ?int
    {
        return $this->category_id;
    }

    public function setCategoryId(int $category_id): self
    {
        $this->category_id = $category_id;

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

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }
    

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(string $details): self
    {
        $this->details = $details;

        return $this;
    }
}
