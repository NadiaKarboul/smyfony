<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:"id_commande")]
    #[Groups("commande")]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false,name:"id_panier",referencedColumnName:"id_panier")]
    #[Groups("commande")]
    private ?Panier $panier = null;

    #[ORM\Column]
    #[Groups("commande")]
    private ?float $montant = null;

    #[ORM\Column(length: 200)]
    #[Groups("commande")]
    private ?string $etat_commande = null;

    #[ORM\Column(length: 200)]
    #[Assert\NotBlank(message:"Le nom du gouvernorat est obligatoire !")]
    #[Groups("commande")]
    private ?string $gouvernorat = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message:"Le nom de la ville est obligatoire !")]
    #[Groups("commande")]
    private ?string $ville = null;

    #[ORM\Column(length: 200)]
    #[Assert\NotBlank(message:"Le nom de la rue est obligatoire !")]
    #[Groups("commande")]
    private ?string $rue = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"Le code postal est obligatoire !")]
    #[Assert\Positive(message:"Le code postal doit etre un nombre positif !")]
    #[Assert\Length(exactly: 4, exactMessage : "Le code postal doit etre un nombre de quatre chiffres")]
    #[Groups("commande")]
    private ?int $code_postal = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups("commande")]
    private ?\DateTimeInterface $date_commande  ;
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    public function setPanier(?Panier $panier): self
    {
        $this->panier = $panier;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getEtatCommande(): ?string
    {
        return $this->etat_commande;
    }

    public function setEtatCommande(string $etat_commande): self
    {
        $this->etat_commande = $etat_commande;

        return $this;
    }

    public function getGouvernorat(): ?string
    {
        return $this->gouvernorat;
    }

    public function setGouvernorat(string $gouvernorat): self
    {
        $this->gouvernorat = $gouvernorat;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(string $rue): self
    {
        $this->rue = $rue;

        return $this;
    }

    public function getCodePostal(): ?int
    {
        return $this->code_postal;
    }

    public function setCodePostal(int $code_postal): self
    {
        $this->code_postal = $code_postal;

        return $this;
    }

    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->date_commande;
    }

    public function setDateCommande(\DateTimeInterface $date_commande): self
    {
        $this->date_commande = $date_commande;

        return $this;
    }
}