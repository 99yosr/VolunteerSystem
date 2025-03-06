<?php
/*
namespace App\Entity;

use App\Repository\VolontaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VolontaireRepository::class)]
class Volontaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $phoneNum = null;

    #[ORM\Column(length: 255)]
    private ?string $skills = null;

    #[ORM\Column]
    private ?bool $availability = null;

    /**
     * @var Collection<int, Participer>
     */
     /*#[ORM\OneToMany(targetEntity: Participer::class, mappedBy: 'volontaire')]
    private Collection $participer;

    /**
     * @var Collection<int, Inscription>
     */
  /*  #[ORM\OneToMany(targetEntity: Inscription::class, mappedBy: 'volontaire')]
    private Collection $inscription;

    public function __construct()
    {
        $this->participer = new ArrayCollection();
        $this->inscription = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdV(): ?int
    {
        return $this->id_V;
    }

    public function setIdV(int $id_V): static
    {
        $this->id_V = $id_V;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNum(): ?string
    {
        return $this->phoneNum;
    }

    public function setPhoneNum(string $phoneNum): static
    {
        $this->phoneNum = $phoneNum;

        return $this;
    }

    public function getSkills(): ?string
    {
        return $this->skills;
    }

    public function setSkills(string $skills): static
    {
        $this->skills = $skills;

        return $this;
    }

    public function isAvailability(): ?bool
    {
        return $this->availability;
    }

    public function setAvailability(bool $availability): static
    {
        $this->availability = $availability;

        return $this;
    }

    /**
     * @return Collection<int, Participer>
     */
  /*  public function getParticiper(): Collection
    {
        return $this->participer;
    }

    public function addParticiper(Participer $participer): static
    {
        if (!$this->participer->contains($participer)) {
            $this->participer->add($participer);
            $participer->setVolontaire($this);
        }

        return $this;
    }

    public function removeParticiper(Participer $participer): static
    {
        if ($this->participer->removeElement($participer)) {
            // set the owning side to null (unless already changed)
            if ($participer->getVolontaire() === $this) {
                $participer->setVolontaire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Inscription>
     */
/*    public function getInscription(): Collection
    {
        return $this->inscription;
    }

    public function addInscription(Inscription $inscription): static
    {
        if (!$this->inscription->contains($inscription)) {
            $this->inscription->add($inscription);
            $inscription->setVolontaire($this);
        }

        return $this;
    }

    public function removeInscription(Inscription $inscription): static
    {
        if ($this->inscription->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getVolontaire() === $this) {
                $inscription->setVolontaire(null);
            }
        }

        return $this;
    }
}
*/