<?php
/*
namespace App\Entity;

use App\Repository\AssociationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AssociationRepository::class)]
class Association
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $numTel = null;

    #[ORM\Column(length: 255)]
    private ?string $emailAssociation = null;

    #[ORM\Column(length: 255)]
    private ?string $passwordA = null;
    #[ORM\Column(length: 255)]
    private ?string $description = null;
    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    private ?string $local = null;

    #[ORM\Column(length: 255)]
    private ?string $ceo = null;

    /**
     * @var Collection<int, Event>
     */
   /** #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'association')]
    private Collection $events;

    /**
     * @var Collection<int, Formation>
     */
 /**   #[ORM\OneToMany(targetEntity: Formation::class, mappedBy: 'association')]
    private Collection $formations;**/

   /* public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->formations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdA(): ?int
    {
        return $this->id_A;
    }
    public function getImage(): ?string
    {
        return $this->image;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setIdA(int $id_A): static
    {
        $this->id_A = $id_A;

        return $this;
    }
    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }
    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getNumTel(): ?string
    {
        return $this->numTel;
    }

    public function setNumTel(string $numTel): static
    {
        $this->numTel = $numTel;

        return $this;
    }

    public function getEmailAssociation(): ?string
    {
        return $this->emailAssociation;
    }

    public function setEmailAssociation(string $emailAssociation): static
    {
        $this->emailAssociation = $emailAssociation;

        return $this;
    }

    public function getPasswordA(): ?string
    {
        return $this->passwordA;
    }

    public function setPasswordA(string $passwordA): static
    {
        $this->passwordA = $passwordA;

        return $this;
    }

    public function getLocal(): ?string
    {
        return $this->local;
    }

    public function setLocal(string $local): static
    {
        $this->local = $local;

        return $this;
    }

    public function getCeo(): ?string
    {
        return $this->ceo;
    }

    public function setCeo(string $ceo): static
    {
        $this->ceo = $ceo;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    /*public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setAssociation($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getAssociation() === $this) {
                $event->setAssociation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Formation>
     */
    /*public function getFormations(): Collection
    {
        return $this->formations;
    }

    public function addFormation(Formation $formation): static
    {
        if (!$this->formations->contains($formation)) {
            $this->formations->add($formation);
            $formation->setAssociation($this);
        }

        return $this;
    }

    public function removeFormation(Formation $formation): static
    {
        if ($this->formations->removeElement($formation)) {
            // set the owning side to null (unless already changed)
            if ($formation->getAssociation() === $this) {
                $formation->setAssociation(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return $this->id;
    }
}*/
