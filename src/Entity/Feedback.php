<?php

namespace App\Entity;

use App\Repository\FeedbackRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: FeedbackRepository::class)]
class Feedback
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:"id_feedback")]
    #[Groups("comment")]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\Type(
        type: 'integer',
        message: "the value {{ value }} is not a valid {{ type }}.",
    )]
    #[Assert\GreaterThanOrEqual(
        value: 0,
        message: 'the rate must be positive',
    )]
    #[Assert\LessThanOrEqual(
        value: 5,
        message: 'the rate must be less than or equal to 5.',
    )]
    #[Assert\NotBlank(message:"The rate is mandatory !")]
    #[Groups("comment")]
    private ?int $rate = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message:"The comment is mandatory !")]
    #[Groups("comment")]
    private ?string $commentaire = null;

    #[ORM\Column]
    #[Groups("comment")]
    private ?bool $report = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false,name:"id_user",referencedColumnName:"id_user")]
    #[Groups("comment")]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false,name:"id_bonplan",referencedColumnName:"id_bonplan")]
    #[Groups("comment")]
    private ?Bonplan $bonplan = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRate(): ?int
    {
        return $this->rate;
    }

    public function setRate(int $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function isReport(): ?bool
    {
        return $this->report;
    }

    public function setReport(bool $report): self
    {
        $this->report = $report;

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

    public function getBonplan(): ?Bonplan
    {
        return $this->bonplan;
    }

    public function setBonplan(?Bonplan $bonplan): self
    {
        $this->bonplan = $bonplan;

        return $this;
    }
}
