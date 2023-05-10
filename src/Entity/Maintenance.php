<?php

namespace App\Entity;

use App\Repository\MaintenanceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MaintenanceRepository::class)]
class Maintenance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:"id_maintenance")]
    #[Groups("maintenance")]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false,name:"id_user",referencedColumnName:"id_user")]
    #[Groups("maintenance")]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups("maintenance")]
    private ?\DateTimeInterface $date_maintenance = null;

    #[ORM\Column]
    #[Groups("maintenance")]
    private ?bool $panne_moteur = null;

    #[ORM\Column]
    #[Groups("maintenance")]
    private ?bool $pompe_a_eau = null;

    #[ORM\Column]
    #[Groups("maintenance")]
    private ?bool $patin = null;

    #[ORM\Column]
    #[Groups("maintenance")]
    private ?bool $essuie_glace = null;

    #[ORM\Column]
    #[Groups("maintenance")]
    private ?bool $radiateur = null;

    #[ORM\Column]
    #[Groups("maintenance")]
    private ?bool $ventilateur = null;

    #[ORM\Column]
    #[Groups("maintenance")]
    private ?bool $duride = null;

    #[ORM\Column]
    #[Groups("maintenance")]
    private ?bool $fuite_d_huile = null;

    #[ORM\Column]
    #[Groups("maintenance")]
    private ?bool $vidange = null;

    #[ORM\Column]
    #[Groups("maintenance")]
    private ?bool $filtre = null;

    #[ORM\Column]
    #[Groups("maintenance")]
    private ?bool $batterie = null;

    #[ORM\Column]
    #[Groups("maintenance")]
    private ?bool $amortisseur = null;

    #[ORM\Column]
    #[Groups("maintenance")]
    private ?bool $frein_main = null;

    #[ORM\Column]
    #[Groups("maintenance")]
    private ?bool $feu_d_eclairage = null;

    #[ORM\Column(length:255,nullable:true)]
    private ?string $Autre = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDateMaintenance(): ?\DateTimeInterface
    {
        return $this->date_maintenance;
    }

    public function setDateMaintenance(\DateTimeInterface $date_maintenance): self
    {
        $this->date_maintenance = $date_maintenance;

        return $this;
    }

    public function isPanneMoteur(): ?bool
    {
        return $this->panne_moteur;
    }

    public function setPanneMoteur(bool $panne_moteur): self
    {
        $this->panne_moteur = $panne_moteur;

        return $this;
    }

    public function isPompeAEau(): ?bool
    {
        return $this->pompe_a_eau;
    }

    public function setPompeAEau(bool $pompe_a_eau): self
    {
        $this->pompe_a_eau = $pompe_a_eau;

        return $this;
    }

    public function isPatin(): ?bool
    {
        return $this->patin;
    }

    public function setPatin(bool $patin): self
    {
        $this->patin = $patin;

        return $this;
    }

    public function isEssuieGlace(): ?bool
    {
        return $this->essuie_glace;
    }

    public function setEssuieGlace(bool $essuie_glace): self
    {
        $this->essuie_glace = $essuie_glace;

        return $this;
    }

    public function isRadiateur(): ?bool
    {
        return $this->radiateur;
    }

    public function setRadiateur(bool $radiateur): self
    {
        $this->radiateur = $radiateur;

        return $this;
    }

    public function isVentilateur(): ?bool
    {
        return $this->ventilateur;
    }

    public function setVentilateur(bool $ventilateur): self
    {
        $this->ventilateur = $ventilateur;

        return $this;
    }

    public function isDuride(): ?bool
    {
        return $this->duride;
    }

    public function setDuride(bool $duride): self
    {
        $this->duride = $duride;

        return $this;
    }

    public function isFuiteDHuile(): ?bool
    {
        return $this->fuite_d_huile;
    }

    public function setFuiteDHuile(bool $fuite_d_huile): self
    {
        $this->fuite_d_huile = $fuite_d_huile;

        return $this;
    }

    public function isVidange(): ?bool
    {
        return $this->vidange;
    }

    public function setVidange(bool $vidange): self
    {
        $this->vidange = $vidange;

        return $this;
    }

    public function isFiltre(): ?bool
    {
        return $this->filtre;
    }

    public function setFiltre(bool $filtre): self
    {
        $this->filtre = $filtre;

        return $this;
    }

    public function isBatterie(): ?bool
    {
        return $this->batterie;
    }

    public function setBatterie(bool $batterie): self
    {
        $this->batterie = $batterie;

        return $this;
    }

    public function isAmortisseur(): ?bool
    {
        return $this->amortisseur;
    }

    public function setAmortisseur(bool $amortisseur): self
    {
        $this->amortisseur = $amortisseur;

        return $this;
    }

    public function isFreinMain(): ?bool
    {
        return $this->frein_main;
    }

    public function setFreinMain(bool $frein_main): self
    {
        $this->frein_main = $frein_main;

        return $this;
    }

    public function isFeuDEclairage(): ?bool
    {
        return $this->feu_d_eclairage;
    }

    public function setFeuDEclairage(bool $feu_d_eclairage): self
    {
        $this->feu_d_eclairage = $feu_d_eclairage;

        return $this;
    }

    public function getAutre(): ?string
    {
        return $this->Autre;
    }

    public function setAutre(string $Autre): self
    {
        $this->Autre = $Autre;

        return $this;
    }
}