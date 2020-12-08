<?php

namespace App\Entity\Main;

use App\Repository\Main\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\This;

/**
 * @ORM\Entity(repositoryClass=NotificationRepository::class)
 */
class Notification
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="notifications")
     */
    private $owner;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $readed;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $link;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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

    public function getReaded(): ?bool
    {
        return $this->readed;
    }

    public function setReaded(bool $readed): self
    {
        $this->readed = $readed;

        return $this;
    }

    public function construct($owner, int $value, array $informations, $request) : self
    {
        define('SERVER', $link = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath());
        $notif = $this;
     
        switch($value) {

            // FORUM RESPONSE TOPIC
            case 0:
                $type = "FORUM";
                $sample_msg = $informations['interlocutor']." a répondu à votre topic '".$informations['topic']->getTitle()."'.";
                $sub_category = $informations['topic']->getSubcategory();
                $category = $sub_category->getCategory();
                $link = SERVER.'/forum/'.$category->getId().'.'.$sub_category->getId().'/'.$informations['topic']->get_url_custom_encode_title().'.'.$informations['topic']->getId();

                $notif->setOwner($owner)
                    ->setDescription($sample_msg)
                    ->setType($type)
                    ->setReaded(false)
                    ->setLink($link);
                break;

            // SUPPORT RESPONSE
            case 1:
                $type = "SUPPORT";
                $sample_msg = $informations['interlocutor']." a répondu à votre ticket '".$informations['ticket']->getTitle()."'.";
                $link = SERVER.'/support/'.$informations['ticket']->getTitle().'.'.$informations['ticket']->getId();

                $notif->setOwner($owner)
                    ->setDescription($sample_msg)
                    ->setType($type)
                    ->setReaded(false)
                    ->setLink($link);
                break;

        }

        return $notif;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }
}
