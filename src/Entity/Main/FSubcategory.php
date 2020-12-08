<?php

namespace App\Entity\Main;

use App\Repository\Main\FSubcategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FSubcategoryRepository::class)
 */
class FSubcategory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=FCategory::class, inversedBy="subcategories")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=FTopic::class, mappedBy="subcategory")
     */
    private $topics;

    public function __construct()
    {
        $this->topics = new ArrayCollection();
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

    public function getCategory(): ?FCategory
    {
        return $this->category;
    }

    public function setCategory(?FCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|FTopic[]
     */
    public function getTopics(): Collection
    {
        return $this->topics;
    }

    public function addTopic(FTopic $topic): self
    {
        if (!$this->topics->contains($topic)) {
            $this->topics[] = $topic;
            $topic->setSubcategory($this);
        }

        return $this;
    }

    public function removeTopic(FTopic $topic): self
    {
        if ($this->topics->contains($topic)) {
            $this->topics->removeElement($topic);
            // set the owning side to null (unless already changed)
            if ($topic->getSubcategory() === $this) {
                $topic->setSubcategory(null);
            }
        }

        return $this;
    }
}
