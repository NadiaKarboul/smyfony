<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
#[ORM\Entity(repositoryClass: TicketRepository::class)]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:"id_ticket")]
    #[Groups("ticket")]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups("ticket")]
    private ?int $num_ticket = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false,referencedColumnName:"id_user",name:"id_spectateur")]
    #[Groups("ticket")]
    public ?User $id_spectateur = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false,name:"id_e",referencedColumnName:"id_e")]
    #[Groups("ticket")]
    private ?Event $event = null;

    #[ORM\Column(length: 255)]
    #[Groups("ticket")]
    private ?string $image_qr = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumTicket(): ?int
    {
        return $this->num_ticket;
    }

    public function setNumTicket(int $num_ticket): self
    {
        $this->num_ticket = $num_ticket;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->id_spectateur;
    }

    public function setUser(?User $id_spectateur): self
    {
        $this->id_spectateur = $id_spectateur;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getImageQr(): ?string
    {
        return $this->image_qr;
    }

    public function setImageQr(string $image_qr): self
    {
        $this->image_qr = $image_qr;

        return $this;
    }
}
