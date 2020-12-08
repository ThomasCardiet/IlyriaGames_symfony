<?php

namespace App\Entity\Main;

use App\Repository\Main\PaymentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PaymentRepository::class)
 */
class Payment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $owner;

    /**
     * @ORM\Column(type="datetime")
     */
    private $create_At;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mode;

    /**
     * @ORM\Column(type="integer")
     */
    private $pb;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getOwner(): ?string
    {
        return $this->owner;
    }

    public function setOwner(string $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->create_At;
    }

    public function setCreateAt(\DateTimeInterface $create_At): self
    {
        $this->create_At = $create_At;

        return $this;
    }

    public function getMode(): ?string
    {
        return $this->mode;
    }

    public function setMode(string $mode): self
    {
        $this->mode = $mode;

        return $this;
    }

    public function getPb(): ?int
    {
        return $this->pb;
    }

    public function setPb(int $pb): self
    {
        $this->pb = $pb;

        return $this;
    }
}
