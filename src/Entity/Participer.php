<?php

namespace App\Entity;

use App\Repository\ParticiperRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticiperRepository::class)]
class Participer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $etat = null;

    #[ORM\ManyToOne(inversedBy: 'participer')]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserV $userv = null;

    #[ORM\ManyToOne(inversedBy: 'participer')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $event = null;



    public function getId(): ?int
    {
        return $this->id;
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

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;

        return $this;
    }
}
