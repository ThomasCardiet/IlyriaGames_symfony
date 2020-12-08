<?php

namespace App\Entity\Main;

use App\Repository\Main\FMessageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FMessageRepository::class)
 */
class FMessage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $create_at;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="fMessages")
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity=FTopic::class, inversedBy="messages")
     */
    private $topic;


    public function getId(): ?int
    {
        return $this->id;
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

    public function getOwner(): ?Users
    {
        return $this->owner;
    }

    public function setOwner(?Users $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getTopic(): ?FTopic
    {
        return $this->topic;
    }

    public function setTopic(?FTopic $topic): self
    {
        $this->topic = $topic;

        return $this;
    }
}
