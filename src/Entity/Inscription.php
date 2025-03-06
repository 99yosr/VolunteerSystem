<?php

namespace App\Entity;

use App\Repository\InscriptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InscriptionRepository::class)]
class Inscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateI = null;

    #[ORM\Column(length: 255)]
    private ?string $etat = null;

    #[ORM\ManyToOne(inversedBy: 'inscription')]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserV $userv = null;

    #[ORM\ManyToOne(inversedBy: 'inscription')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Formation $formation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateI(): ?\DateTimeInterface
    {
        return $this->dateI;
    }

    public function setDateI(\DateTimeInterface $dateI): static
    {
        $this->dateI = $dateI;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getVolontaire(): ?UserV
    {
        if ($this->userv && in_array('ROLE_VOLONTAIRE', $this->userv->getRoles())) {
            return $this->userv;
        }
        return null;
    }

    public function setVolontaire(?UserV $volontaire): static
    {
        if ($volontaire && in_array('ROLE_VOLONTAIRE', $volontaire->getRoles())) {
            $this->userv = $volontaire;
        } else {
            throw new \InvalidArgumentException('The provided user must have the ROLE_VOLONTAIRE role.');
        }

        return $this;
    }

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): static
    {
        $this->formation = $formation;

        return $this;
    }
}
