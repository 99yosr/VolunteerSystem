<?php

namespace App\Entity;

use App\Repository\UserVRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserVRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class UserV implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(nullable: true)]
    private array $roles = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $numTel = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $local = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ceo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $skills = null;

    #[ORM\Column(nullable: true)]
    private ?bool $availability = null;

    #[ORM\Column(nullable: true)]
    private ?string $password = null;

    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'userv')]
    private Collection $events;
    #[ORM\OneToMany(targetEntity: Formation::class, mappedBy: 'userv')]
    private Collection $formations;
    #[ORM\OneToMany(targetEntity: Participer::class, mappedBy: 'userv')]
    private Collection $participer;
    #[ORM\OneToMany(targetEntity: Inscription::class, mappedBy: 'userv')]
    private Collection $inscription;

    public function __construct()
    {
        $this->participer = new ArrayCollection();
        $this->inscription = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->formations = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getname(): ?string
    {
        return $this->name;
    }

    public function setname(?string $name): void
    {
        $this->name = $name;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTimeInterface $dateCreation): void
    {
        $this->dateCreation = $dateCreation;
    }

    public function getNumTel(): ?string
    {
        return $this->numTel;
    }

    public function setNumTel(?string $numTel): void
    {
        $this->numTel = $numTel;
    }

    /*public function getEmailAssociation(): ?string
    {
        return $this->emailAssociation;
    }

    public function setEmailAssociation(?string $emailAssociation): void
    {
        $this->emailAssociation = $emailAssociation;
    }*/

    /*public function getPasswordA(): ?string
    {
        return $this->passwordA;
    }

    public function setPasswordA(?string $passwordA): void
    {
        $this->passwordA = $passwordA;
    }
*/
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    public function getLocal(): ?string
    {
        return $this->local;
    }

    public function setLocal(?string $local): void
    {
        $this->local = $local;
    }

    public function getCeo(): ?string
    {
        return $this->ceo;
    }

    public function setCeo(?string $ceo): void
    {
        $this->ceo = $ceo;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /*public function getPhoneNum(): ?string
    {
        return $this->phoneNum;
    }

    public function setPhoneNum(?string $phoneNum): void
    {
        $this->phoneNum = $phoneNum;
    }*/

    public function getSkills(): ?string
    {
        return $this->skills;
    }

    public function setSkills(?string $skills): void
    {
        $this->skills = $skills;
    }

    public function getAvailability(): ?bool
    {
        return $this->availability;
    }

    public function setAvailability(?bool $availability): void
    {
        $this->availability = $availability;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
    public function getParticiper(): Collection
    {
        return $this->participer;
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
    public function getInscription(): Collection
    {
        return $this->inscription;
    }
    public function getEvents(): Collection
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
    public function getFormations(): Collection
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
}
