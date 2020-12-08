<?php

namespace App\Entity\Main;

use App\Repository\Main\VisitRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VisitRepository::class)
 */
class Visit
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $ip_adresse;

    /**
     * @ORM\Column(type="datetime")
     */
    private $create_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIpAdresse(): ?string
    {
        return $this->ip_adresse;
    }

    public function setIpAdresse(string $ip_adresse): self
    {
        $this->ip_adresse = $ip_adresse;

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
}
