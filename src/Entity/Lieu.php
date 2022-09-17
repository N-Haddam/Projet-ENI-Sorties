<?php

namespace App\Entity;

use App\Repository\LieuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LieuRepository::class)]
class Lieu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['liste_lieux_par_ville'])]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(max: 60)]
    #[Assert\Regex('/^[ 0-9A-Za-zÀ-ÖØ-öø-ÿ]+$/')]
    #[Groups(['liste_lieux_par_ville'])]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(max: 100)]
    #[Assert\Regex('/^[ 0-9A-Za-zÀ-ÖØ-öø-ÿ]+$/')]
    #[Groups(['liste_lieux_par_ville'])]
    private ?string $rue = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['liste_lieux_par_ville'])]
    private ?float $latitude = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['liste_lieux_par_ville'])]
    private ?float $longitude = null;

    #[ORM\ManyToOne(inversedBy: 'lieus')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['liste_lieux_par_ville'])]
    private ?Ville $ville = null;

    #[ORM\OneToMany(mappedBy: 'lieu', targetEntity: Sortie::class, orphanRemoval: true)]
    private Collection $sorties;

    public function __construct()
    {
        $this->sorties = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(string $rue): self
    {
        $this->rue = $rue;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): self
    {
        $this->ville = $ville;

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
            $sorty->setLieu($this);
        }

        return $this;
    }

    public function removeSorty(Sortie $sorty): self
    {
        if ($this->sorties->removeElement($sorty)) {
            // set the owning side to null (unless already changed)
            if ($sorty->getLieu() === $this) {
                $sorty->setLieu(null);
            }
        }

        return $this;
    }

}
