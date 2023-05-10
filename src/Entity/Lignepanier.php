<?php

namespace App\Entity;

use App\Repository\LignepanierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: LignepanierRepository::class)]
class Lignepanier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:"id_ligne")]
    #[Groups("lignepanier")]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false,referencedColumnName:"id_prod",name:"id_produit")]
    #[Groups("lignepanier")]
    private ?Produit $produit = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false,name:"id_panier",referencedColumnName:"id_panier")]
    #[Groups("lignepanier")]
    private ?Panier $panier = null;

    #[ORM\Column]
    #[Groups("lignepanier")]
    private ?int $quantite = null;
 

    #[ORM\Column]
    #[Groups("lignepanier")]
    private ?float $prix = null;

    #[ORM\Column(length: 30)]
    #[Groups("lignepanier")]
    private ?string $nom_produit = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups("lignepanier")]
    private ?string $description_prod = null;

    #[ORM\Column(length: 255)]
    #[Groups("lignepanier")]
    private ?string $image = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
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

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getNomProduit(): ?string
    {
        return $this->nom_produit;
    }

    public function setNomProduit(string $nom_produit): self
    {
        $this->nom_produit = $nom_produit;

        return $this;
    }

    public function getDescriptionProd(): ?string
    {
        return $this->description_prod;
    }

    public function setDescriptionProd(string $description_prod): self
    {
        $this->description_prod = $description_prod;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }
}
