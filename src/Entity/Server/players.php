<?php

namespace App\Entity\Server;

use App\AppBundle\RankMethods;
use App\Repository\Server\playersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=playersRepository::class)
 */
class players
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
    private $uuid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $amis;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pseudo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $server;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $groupe;

    /**
     * @ORM\Column(type="integer")
     */
    private $pj;

    /**
     * @ORM\Column(type="integer")
     */
    private $pb;

    /**
     * @ORM\Column(type="blob")
     */
    private $cosmetic;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Ip;

    public function __construct()
    {
        $this->topics = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getAmis()
    {
        return $this->amis;
    }

    public function setAmis(String $amis): ?string
    {
        $this->amis = $amis;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getServer(): ?string
    {
        return $this->server;
    }

    public function setServer(string $server): self
    {
        $this->server = $server;

        return $this;
    }

    public function getGroupe(): ?string
    {
        return $this->groupe;
    }

    public function setGroupe(string $groupe): self
    {
        $this->groupe = $groupe;

        return $this;
    }

    public function getPj(): ?int
    {
        return $this->pj;
    }

    public function setPj(int $pj): self
    {
        $this->pj = $pj;

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

    public function getCosmetic()
    {
        return $this->cosmetic;
    }

    public function setCosmetic($cosmetic): self
    {
        $this->cosmetic = $cosmetic;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->Ip;
    }

    public function setIp(string $Ip): self
    {
        $this->Ip = $Ip;

        return $this;
    }

    public function getRankColor(){

        return (new RankMethods())->getRank($this->groupe)['color'];
    }

    function isAdmin(){
        $power = (new RankMethods())->getRank($this->groupe)['power'];
        if($power>=5)return true;
        return false;

    }
}
