<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{
    #[ORM\Id]
    #[ORM\Column(name:"id_panier")]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false,name:"id_user",referencedColumnName:"id_user")]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getUser(): ?User
    {
        return $this->user;
    }
public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
