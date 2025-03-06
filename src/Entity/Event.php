<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nameEvent = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateEvent = null;
    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateEventF = null;
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;
    #[ORM\Column(length: 255)]
    private ?string $location = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    /**
     * @var Collection<int, Participer>
     */
    #[ORM\OneToMany(targetEntity: Participer::class, mappedBy: 'event')]
    private Collection $participer;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserV $userv = null;

    public function __construct()
    {
        $this->participer = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdE(): ?int
    {
        return $this->id_E;
    }

    public function setIdE(int $id_E): static
    {
        $this->id_E = $id_E;

        return $this;
    }

    public function getNameEvent(): ?string
    {
        return $this->nameEvent;
    }

    public function setNameEvent(string $nameEvent): static
    {
        $this->nameEvent = $nameEvent;

        return $this;
    }

    public function getDateEvent(): ?\DateTimeInterface
    {
        return $this->dateEvent;
    }

    public function getDateEventF(): ?\DateTimeInterface
    {
        return $this->dateEventF;
    }

    public function setDateEvent(\DateTimeInterface $dateEvent): static
    {
        $this->dateEvent = $dateEvent;

        return $this;
    }
    public function setDateEventF(\DateTimeInterface $dateEventF): static
    {
        $this->dateEventF = $dateEventF;

        return $this;
    }
    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }
    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
    }
    /**
     * @return Collection<int, Participer>
     */
    public function getParticiper(): Collection
    {
        return $this->participer;
    }

    public function addParticiper(Participer $participer): static
    {
        if (!$this->participer->contains($participer)) {
            $this->participer->add($participer);
            $participer->setEvent($this);
        }

        return $this;
    }

    public function removeParticiper(Participer $participer): static
    {
        if ($this->participer->removeElement($participer)) {
            // set the owning side to null (unless already changed)
            if ($participer->getEvent() === $this) {
                $participer->setEvent(null);
            }
        }

        return $this;
    }

    public function getAssociation(): ?UserV
    {
        if ($this->userv && in_array('ROLE_ASSOCIATION', $this->userv->getRoles())) {
            return $this->userv;
        }
        return null;
    }

    public function setAssociation(?UserV $user): static
    {
        if ($user && in_array('ROLE_ASSOCIATION', $user->getRoles())) {
            $this->userv = $user;
        } else {
            throw new \InvalidArgumentException('The provided user must have the ROLE_ASSOCIATION role.');
        }

        return $this;
    }
}
