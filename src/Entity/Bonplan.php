<?php

namespace App\Entity;

use App\Repository\BonplanRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BonplanRepository::class)]
class Bonplan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:"id_bonplan")]
    #[Groups("bonplan")]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups("bonplan")]
    #[Assert\NotBlank(message:"The name of the Bonplan is mandatory !")]
    #[Assert\Regex(
        pattern: '/^[A-Za-z]+$/',
        message: 'The name of the Bonplan must contain only letters.',
    )]
    private ?string $nom_bonplan ;

    #[ORM\Column(length: 50)]
    #[Groups("bonplan")]
    #[Assert\NotBlank(message:"The governorate is mandatory !")]
    #[Assert\Regex(
        pattern: '/^[A-Za-z]+$/',
        message: 'The name of the governorate must contain only letters.',
    )]
    private ?string $gouvernorat ;

    #[ORM\Column(length: 50)]
    #[Groups("bonplan")]
    #[Assert\NotBlank(message:"The city is mandatory !")]
    #[Assert\Regex(
        pattern: '/^[A-Za-z]+$/',
        message: 'The name of the city must contain only letters.',
    )]
    private ?string $ville ;

    #[ORM\Column(length: 255)]
    #[Groups("bonplan")]
    #[Assert\NotBlank(message:"The street is mandatory !")]
    private ?string $rue ;

    #[ORM\Column(length: 255)]
    #[Groups("bonplan")]
    #[Assert\NotBlank(message:"The description is mandatory !")]
    private ?string $description ;

    #[ORM\Column(length: 50)]
    #[Groups("bonplan")]
    #[Assert\NotBlank(message:"The type is mandatory !")]
    private ?string $type ;

    #[ORM\Column(length: 20)]
    #[Groups("bonplan")]
    private ?string $etat ;

    #[ORM\Column(length: 255)]
    #[Groups("bonplan")]
    // #[Assert\NotBlank(message:"L'image est obligatoire !")]
    private ?string $image ;

    #[ORM\ManyToOne]
    #[Groups("bonplan")]
    #[ORM\JoinColumn(nullable: false,name:"id_user",referencedColumnName:"id_user")]
    private ?User $user ;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomBonplan(): ?string
    {
        return $this->nom_bonplan;
    }

    public function setNomBonplan(string $nom_bonplan): self
    {
        $this->nom_bonplan = $nom_bonplan;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
