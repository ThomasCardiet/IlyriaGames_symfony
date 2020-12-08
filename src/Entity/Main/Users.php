<?php

namespace App\Entity\Main;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\Main\UsersRepository;

/**
 * @ORM\Entity(repositoryClass=UsersRepository::class)
 */
class Users implements UserInterface
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
    private $pseudo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     */
    private $votes;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $confirmkey;

    /**
     * @ORM\OneToMany(targetEntity=FTopic::class, mappedBy="owner")
     */
    private $topics;

    /**
     * @ORM\OneToMany(targetEntity=FMessage::class, mappedBy="owner")
     */
    private $messages;

    /**
     * @ORM\OneToMany(targetEntity=Ticket::class, mappedBy="owner")
     */
    private $tickets;

    /**
     * @ORM\OneToMany(targetEntity=TMessage::class, mappedBy="owner")
     */
    private $tickets_messages;

    /**
     * @ORM\OneToMany(targetEntity=news::class, mappedBy="owner")
     */
    private $news;

    /**
     * @ORM\OneToMany(targetEntity=NComments::class, mappedBy="owner")
     */
    private $newsComments;

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="owner")
     */
    private $notifications;


    public function __construct()
    {
        $this->topics = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->tickets = new ArrayCollection();
        $this->tickets_messages = new ArrayCollection();
        $this->news = new ArrayCollection();
        $this->newsComments = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getVotes(): ?int
    {
        return $this->votes;
    }

    public function setVotes(int $votes): self
    {
        $this->votes = $votes;

        return $this;
    }

    public function getConfirmkey(): ?string
    {
        return $this->confirmkey;
    }

    public function setConfirmkey(string $confirmkey): self
    {
        $this->confirmkey = $confirmkey;

        return $this;
    }

    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function getSalt() {}

    public function getUsername()
    {
        return $this->pseudo;
    }

    public function eraseCredentials() {}

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
            $topic->setOwner($this);
        }

        return $this;
    }

    public function removeTopic(FTopic $topic): self
    {
        if ($this->topics->contains($topic)) {
            $this->topics->removeElement($topic);
            // set the owning side to null (unless already changed)
            if ($topic->getOwner() === $this) {
                $topic->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|FMessage[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(FMessage $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setOwner($this);
        }

        return $this;
    }

    public function removeMessage(FMessage $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getOwner() === $this) {
                $message->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Ticket[]
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets[] = $ticket;
            $ticket->setOwner($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->contains($ticket)) {
            $this->tickets->removeElement($ticket);
            // set the owning side to null (unless already changed)
            if ($ticket->getOwner() === $this) {
                $ticket->setOwner(null);
            }
        }

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
            $ticketsMessage->setOwner($this);
        }

        return $this;
    }

    public function removeTicketsMessage(TMessage $ticketsMessage): self
    {
        if ($this->tickets_messages->contains($ticketsMessage)) {
            $this->tickets_messages->removeElement($ticketsMessage);
            // set the owning side to null (unless already changed)
            if ($ticketsMessage->getOwner() === $this) {
                $ticketsMessage->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|news[]
     */
    public function getNews(): Collection
    {
        return $this->news;
    }

    public function addNews(news $news): self
    {
        if (!$this->news->contains($news)) {
            $this->news[] = $news;
            $news->setOwner($this);
        }

        return $this;
    }

    public function removeNews(news $news): self
    {
        if ($this->news->contains($news)) {
            $this->news->removeElement($news);
            // set the owning side to null (unless already changed)
            if ($news->getOwner() === $this) {
                $news->setOwner(null);
            }
        }

        return $this;
    }

    public function setNews(?news $news): self
    {
        $this->news = $news;

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
            $newsComment->setOwner($this);
        }

        return $this;
    }

    public function removeNewsComment(NComments $newsComment): self
    {
        if ($this->newsComments->contains($newsComment)) {
            $this->newsComments->removeElement($newsComment);
            // set the owning side to null (unless already changed)
            if ($newsComment->getOwner() === $this) {
                $newsComment->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setOwner($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getOwner() === $this) {
                $notification->setOwner(null);
            }
        }

        return $this;
    }

}
