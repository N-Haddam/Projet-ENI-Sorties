<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class Participant implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Email]
    #[Assert\Length(max: 180)]
    private ?string $email = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
//    #[Assert\NotBlank]
//    #[Assert\NotNull]
//    #[Assert\NotCompromisedPassword] //TODO : à activer en dehors de la phase de développement
    private ?string $password = null;

    #[ORM\Column(length: 60)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(max: 60)]
    #[Assert\Regex('/^[ 0-9A-Za-zÀ-ÖØ-öø-ÿ]+$/')] // TODO enlever le chiffres en prod
    private ?string $nom = null;

    #[ORM\Column(length: 60)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(max: 60)]
    #[Assert\Regex('/^[ 0-9A-Za-zÀ-ÖØ-öø-ÿ]+$/')] // TODO enlever le chiffres en prod
    private ?string $prenom = null;

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(max: 10)]
    #[Assert\Regex('/^[0-9]{10}$/')]
    private ?string $telephone = null;

    #[ORM\Column]
//    #[Assert\NotBlank]
//    #[Assert\NotNull]
    private ?bool $administrateur = null;

    #[ORM\Column(nullable: false)]
//    #[Assert\NotBlank]
//    #[Assert\NotNull]
    private ?bool $actif = null;

    #[ORM\OneToMany(mappedBy: 'organisateur', targetEntity: Sortie::class, orphanRemoval: true)]
    private Collection $sortiesOrganisees;

    #[ORM\ManyToMany(targetEntity: Sortie::class, mappedBy: 'participants', orphanRemoval: true)]
    private Collection $sorties;

    #[ORM\ManyToOne(inversedBy: 'participants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $campus = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Regex('/^[ 0-9A-Za-zÀ-ÖØ-öø-ÿ]+$/')]
    private ?string $pseudo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pictureFileName = null;

    #[ORM\ManyToMany(targetEntity: GroupePrive::class, mappedBy: 'participants')]
    private Collection $gpPrives;

    #[ORM\OneToMany(mappedBy: 'organisateur', targetEntity: GroupePrive::class, orphanRemoval: true)]
    private Collection $groupePrivesOrganises;

    public function __construct()
    {
        $this->sortiesOrganisees = new ArrayCollection();
        $this->sorties = new ArrayCollection();
        $this->participants = new ArrayCollection();
        $this->groupePrivesOrganises = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
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

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function isAdministrateur(): ?bool
    {
        return $this->administrateur;
    }

    public function setAdministrateur(bool $administrateur): self
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(?bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortiesOrganisees(): Collection
    {
        return $this->sortiesOrganisees;
    }

    public function addSortiesOrganisee(Sortie $sortiesOrganisee): self
    {
        if (!$this->sortiesOrganisees->contains($sortiesOrganisee)) {
            $this->sortiesOrganisees->add($sortiesOrganisee);
            $sortiesOrganisee->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSortiesOrganisee(Sortie $sortiesOrganisee): self
    {
        if ($this->sortiesOrganisees->removeElement($sortiesOrganisee)) {
            // set the owning side to null (unless already changed)
            if ($sortiesOrganisee->getOrganisateur() === $this) {
                $sortiesOrganisee->setOrganisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addSorty(Sortie $sorty): self
    {
        if (!$this->sorties->contains($sorty)) {
            $this->sorties->add($sorty);
            $sorty->addParticipant($this);
        }

        return $this;
    }

    public function removeSorty(Sortie $sorty): self
    {
        if ($this->sorties->removeElement($sorty)) {
            $sorty->removeParticipant($this);
        }

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getPictureFileName(): ?string
    {
        return $this->pictureFileName;
    }

    public function setPictureFileName(?string $pictureFileName): self
    {
        $this->pictureFileName = $pictureFileName;

        return $this;
    }

    public function createdBy(?UserInterface $getUser)
    {

    }

    /**
     * @return Collection<int, GroupePrive>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(GroupePrive $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
            $participant->addParticipant($this);
        }

        return $this;
    }

    public function removeParticipant(GroupePrive $participant): self
    {
        if ($this->participants->removeElement($participant)) {
            $participant->removeParticipant($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, GroupePrive>
     */
    public function getGroupePrivesOrganises(): Collection
    {
        return $this->groupePrivesOrganises;
    }

    public function addGroupePrivesOrganise(GroupePrive $groupePrivesOrganise): self
    {
        if (!$this->groupePrivesOrganises->contains($groupePrivesOrganise)) {
            $this->groupePrivesOrganises->add($groupePrivesOrganise);
            $groupePrivesOrganise->setOrganisateur($this);
        }

        return $this;
    }

    public function removeGroupePrivesOrganise(GroupePrive $groupePrivesOrganise): self
    {
        if ($this->groupePrivesOrganises->removeElement($groupePrivesOrganise)) {
            // set the owning side to null (unless already changed)
            if ($groupePrivesOrganise->getOrganisateur() === $this) {
                $groupePrivesOrganise->setOrganisateur(null);
            }
        }

        return $this;
    }
}
