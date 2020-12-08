<?php

namespace App\Entity\Main;

use App\Repository\Main\newsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=newsRepository::class)
 */
class news
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
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="news")
     */
    private $owner;

    /**
     * @ORM\Column(type="datetime")
     */
    private $create_at;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\OneToMany(targetEntity=NComments::class, mappedBy="news")
     */
    private $newsComments;

    public function __construct()
    {
        $this->newsComments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getOwner(): ?Users
    {
        return $this->owner;
    }

    public function setOwner(?Users $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->create_at;
    }

    public function setCreateAt(\DateTimeInterface $create_at): self
    {
        $this->create_at = $create_at;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection|NComments[]
     */
    public function getNewsComments(): Collection
    {
        return $this->newsComments;
    }

    public function addNewsComment(NComments $newsComment): self
    {
        if (!$this->newsComments->contains($newsComment)) {
            $this->newsComments[] = $newsComment;
            $newsComment->setNews($this);
        }

        return $this;
    }

    public function removeNewsComment(NComments $newsComment): self
    {
        if ($this->newsComments->contains($newsComment)) {
            $this->newsComments->removeElement($newsComment);
            // set the owning side to null (unless already changed)
            if ($newsComment->getNews() === $this) {
                $newsComment->setNews(null);
            }
        }

        return $this;
    }
}
