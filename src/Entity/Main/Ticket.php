<?php

namespace App\Entity\Main;

use App\Repository\Main\TicketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TicketRepository::class)
 */
class Ticket
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="tickets")
     */
    private $owner;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     */
    private $create_at;

    /**
     * @ORM\Column(type="boolean")
     */
    private $resolved;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity=TCategory::class, inversedBy="tickets")
     */
    private $category;

    /**
     * @ORM\Column(type="integer")
     */
    private $priority;

    /**
     * @ORM\OneToMany(targetEntity=TMessage::class, mappedBy="ticket")
     */
    private $tickets_messages;

    public function __construct()
    {
        $this->tickets_messages = new ArrayCollection();
    }

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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getResolved(): ?bool
    {
        return $this->resolved;
    }

    public function setResolved(bool $resolved): self
    {
        $this->resolved = $resolved;

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

    public function getCategory(): ?TCategory
    {
        return $this->category;
    }

    public function setCategory(?TCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return Collection|TMessage[]
     */
    public function getTicketsMessages(): Collection
    {
        return $this->tickets_messages;
    }

    public function addTicketsMessage(TMessage $ticketsMessage): self
    {
        if (!$this->tickets_messages->contains($ticketsMessage)) {
            $this->tickets_messages[] = $ticketsMessage;
            $ticketsMessage->setTicket($this);
        }

        return $this;
    }

    public function removeTicketsMessage(TMessage $ticketsMessage): self
    {
        if ($this->tickets_messages->contains($ticketsMessage)) {
            $this->tickets_messages->removeElement($ticketsMessage);
            // set the owning side to null (unless already changed)
            if ($ticketsMessage->getTicket() === $this) {
                $ticketsMessage->setTicket(null);
            }
        }

        return $this;
    }

    public function getPriorityText() {

        $priorities = array('Faible', 'Moyenne', 'Forte', 'Urgente');

        return $priorities[$this->priority];

    }
}
