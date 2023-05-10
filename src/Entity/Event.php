<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:"id_e")]
    #[Groups("event")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champs est obligatoire")]
    #[Groups("event")]
    private ?string $nom_event = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message:"Ce champs est obligatoire")]
    #[Assert\GreaterThan('today',message:"La date Debut doit etre postérieur a la date d'aujourd'hui")]
    #[Groups("event")]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\Expression("this.getDateDebut() < this.getDateFin()",message:"La date fin doit etre postérieur a la date debut")  ]
    #[Groups("event")]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champs est obligatoire")]
    #[Groups("event")]
    private ?string $lieu_event = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champs est obligatoire")]
    #[Groups("event")]
    private ?string $type_event = null;

    #[ORM\Column(length: 255)]
    #[Groups("event")]
        private ?string $image_event = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"Ce champs est obligatoire")]
    #[Assert\Positive(message:'le prix doit etre positive')]
    #[Groups("event")]
    private ?float $prix_event = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomEvent(): ?string
    {
        return $this->nom_event;
    }

    public function setNomEvent(string $nom_event): self
    {
        $this->nom_event = $nom_event;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): self
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): self
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getLieuEvent(): ?string
    {
        return $this->lieu_event;
    }

    public function setLieuEvent(string $lieu_event): self
    {
        $this->lieu_event = $lieu_event;

        return $this;
    }

    public function getTypeEvent(): ?string
    {
        return $this->type_event;
    }

    public function setTypeEvent(string $type_event): self
    {
        $this->type_event = $type_event;

        return $this;
    }

    public function getImageEvent(): ?string
    {
        return $this->image_event;
    }

    public function setImageEvent(string $image_event): self
    {
        $this->image_event = $image_event;

        return $this;
    }

    public function getPrixEvent(): ?float
    {
        return $this->prix_event;
    }

    public function setPrixEvent(float $prix_event): self
    {
        $this->prix_event = $prix_event;

        return $this;
    }
}
